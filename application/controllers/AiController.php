<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AiController extends MY_Controller
{
    private $user_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('login');
        $this->load->model('Bom', 'bom');
        $this->load->model('Material_issue_model');

        // Verify session login
        $session_data_head = $this->session->userdata('session_data_head');
        if (!$session_data_head) {
            redirect('login');
        }
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);
        
        $this->load->helper('url', 'form');
    }

    /**
     * Dashboard View
     * URL: AiController/index
     */
    public function index()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $prefix = $this->db->dbprefix;

        // 1. Core Triage Calculations
        $sql = "SELECT bt.*, c.company_name, c.fullname,
                       (SELECT COUNT(*) FROM {$prefix}bom b WHERE b.number = bt.number_fk AND b.uid = bt.uid) as item_count
                FROM {$prefix}bom_total bt
                LEFT JOIN {$prefix}customer c ON c.customer_id = bt.customer_id_fk
                WHERE bt.status = 1 AND bt.uid = {$this->user_id}
                ORDER BY bt.date DESC";
        $draft_boms = $this->db->query($sql)->result_array();

        $triaged_list = array();
        $now = time();
        foreach ($draft_boms as $bom) {
            $days_stale = 0;
            if (!empty($bom['date']) && $bom['date'] !== '0000-00-00') {
                $bom_time = strtotime($bom['date']);
                if ($bom_time > 0) {
                    $days_stale = floor(($now - $bom_time) / (60 * 60 * 24));
                }
            }
            $bom['days_stale'] = $days_stale > 0 ? $days_stale : 0;
            $triaged_list[] = $bom;
        }

        $data['total_drafts'] = count($triaged_list);
        $data['stale_count'] = count(array_filter($triaged_list, function($b) { return $b['days_stale'] > 7; }));
        $data['empty_count'] = count(array_filter($triaged_list, function($b) { return $b['item_count'] == 0; }));
        $data['anomaly_count'] = count(array_filter($triaged_list, function($b) { return $b['send_to_mrp'] == 2; }));

        // 2. Fetch Stalled Ages Distribution for Chart.js
        $age_ranges = array('0-3 days' => 0, '4-7 days' => 0, '8-14 days' => 0, '15-30 days' => 0, '30+ days' => 0);
        foreach ($triaged_list as $bom) {
            $age = $bom['days_stale'];
            if ($age <= 3) $age_ranges['0-3 days']++;
            elseif ($age <= 7) $age_ranges['4-7 days']++;
            elseif ($age <= 14) $age_ranges['8-14 days']++;
            elseif ($age <= 30) $age_ranges['15-30 days']++;
            else $age_ranges['30+ days']++;
        }
        $data['chart_labels'] = array_keys($age_ranges);
        $data['chart_data'] = array_values($age_ranges);

        // 3. Central AI Summary of Anomaly Ratio
        $total_boms_count = $this->bom->get_bom_count($this->user_id);
        $anomaly_percent = $total_boms_count > 0 ? round(($data['anomaly_count'] / $total_boms_count) * 100, 1) : 0;
        
        $prompt = "Write a brief, executive summary (max 3 sentences) for a manufacturing manager. Summarize these status metrics:\n" .
                  "- Total Draft BOMs: {$data['total_drafts']}\n" .
                  "- Stale Drafts (>7 days old): {$data['stale_count']}\n" .
                  "- Empty BOMs (0 items): {$data['empty_count']}\n" .
                  "- Process Anomalies (MRP run executed on draft status): {$data['anomaly_count']} representing {$anomaly_percent}% of all BOMs.\n" .
                  "Focus on what process bottlenecks these numbers highlight (especially the process anomalies) and what the immediate management priority should be.";
        
        $data['ai_executive_summary'] = $this->call_groq_ai($prompt);
        $data['settings'] = $this->login->get_settings($this->user_id);

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('ai_insights/dashboard', $data);
    }

    /**
     * BOM & MRP Triage View
     * URL: AiController/bom_triage
     */
    public function bom_triage()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $prefix = $this->db->dbprefix;

        // Fetch all Draft BOMs
        $sql = "SELECT bt.*, c.company_name, c.fullname,
                       (SELECT COUNT(*) FROM {$prefix}bom b WHERE b.number = bt.number_fk AND b.uid = bt.uid) as item_count
                FROM {$prefix}bom_total bt
                LEFT JOIN {$prefix}customer c ON c.customer_id = bt.customer_id_fk
                WHERE bt.status = 1 AND bt.uid = {$this->user_id}
                ORDER BY bt.date DESC";
        $draft_boms = $this->db->query($sql)->result_array();

        $triaged_list = array();
        $now = time();

        foreach ($draft_boms as $bom) {
            $reasons = array();
            $actions = array();
            $severity = 'info';

            // Rule A: Empty BOM
            if ($bom['item_count'] == 0) {
                $reasons[] = "BOM has 0 component items added.";
                $actions[] = "Edit this BOM to search and add components from the item master list.";
                $severity = 'danger';
            }

            // Rule B: Process Anomaly (MRP run on Draft)
            if ($bom['send_to_mrp'] == 2) {
                $reasons[] = "An MRP run was executed on this unapproved Draft BOM.";
                $actions[] = "Submit this BOM for formal approval to align with your production workflow.";
                $severity = 'warning';
            }

            // Rule C: Missing system/assembly name
            if (empty($bom['system'])) {
                $reasons[] = "Assembly / System name is blank.";
                $actions[] = "Open edit form and specify the target system/model.";
                if ($severity !== 'danger') $severity = 'warning';
            }

            // Rule D: Stale Draft (> 7 days)
            $days_stale = 0;
            if (!empty($bom['date']) && $bom['date'] !== '0000-00-00') {
                $bom_time = strtotime($bom['date']);
                if ($bom_time > 0) {
                    $days_stale = floor(($now - $bom_time) / (60 * 60 * 24));
                }
            }

            if ($days_stale > 7) {
                $reasons[] = "BOM has been sitting in Draft status for {$days_stale} days.";
                $actions[] = "If this design is finalized, submit it for approval or archive it.";
                if ($severity === 'info') $severity = 'warning';
            }

            // Rule E: Potential Duplicate Check
            if (!empty($bom['system'])) {
                $system_esc = $this->db->escape($bom['system']);
                $dup_check = $this->db->query("SELECT number_fk FROM {$prefix}bom_total 
                                               WHERE system = {$system_esc} AND status = 4 
                                               AND id != {$bom['id']} AND uid = {$this->user_id} LIMIT 1")->row_array();
                if ($dup_check) {
                    $reasons[] = "Another approved BOM already exists for the same system ({$dup_check['number_fk']}).";
                    $actions[] = "Verify if this revision is a duplicate or an intentional variant/revision.";
                    $severity = 'danger';
                }
            }

            // Fallback
            if (empty($reasons)) {
                $reasons[] = "BOM is a fresh draft with all standard fields populated.";
                $actions[] = "Ready for submission. Click 'Submit for Approval' when design is complete.";
            }

            $bom['triage_reasons'] = $reasons;
            $bom['triage_actions'] = $actions;
            $bom['triage_severity'] = $severity;
            $bom['days_stale'] = $days_stale > 0 ? $days_stale : 0;
            $triaged_list[] = $bom;
        }

        // Count totals
        $data['triaged_boms'] = $triaged_list;
        $data['total_drafts'] = count($triaged_list);
        $data['stale_count'] = count(array_filter($triaged_list, function($b) { return $b['days_stale'] > 7; }));
        $data['empty_count'] = count(array_filter($triaged_list, function($b) { return $b['item_count'] == 0; }));
        $data['anomaly_count'] = count(array_filter($triaged_list, function($b) { return $b['send_to_mrp'] == 2; }));

        $data['settings'] = $this->login->get_settings($this->user_id);

        // Fetch top stale details for executive list box
        $top_stale = array_slice(array_filter($triaged_list, function($b) { return $b['days_stale'] > 0; }), 0, 3);
        if (!empty($top_stale)) {
            $highlights_prompt = "Here are the top 3 stalled Bill of Materials (BOM) in draft status:\n";
            $idx = 1;
            foreach ($top_stale as $b) {
                $reasons_str = implode(', ', $b['triage_reasons']);
                $highlights_prompt .= "{$idx}. BOM {$b['number_fk']} (Age: {$b['days_stale']} days, System: " . ($b['system'] ?: 'Unspecified') . "): {$reasons_str}\n";
                $idx++;
            }
            $highlights_prompt .= "\nWrite a concise summary (max 3 sentences) outlining the core bottlenecks and the immediate actions the engineering team should take to unblock them.";
            $data['ai_highlights'] = $this->call_groq_ai($highlights_prompt);
        } else {
            $data['ai_highlights'] = "All drafts are currently up to date.";
        }

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('ai_insights/bom_triage', $data);
    }

    /**
     * Ask AI Chat View
     * URL: AiController/chat
     */
    public function chat()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $data['settings'] = $this->login->get_settings($this->user_id);

        // Fetch user chat sessions (starred first, then ID descending)
        $sessions = $this->db->where('user_id', $this->user_id)->order_by('is_starred', 'DESC')->order_by('id', 'DESC')->get('ai_chat_sessions')->result_array();
        
        $active_session_id = $this->input->get('session_id');
        
        if (empty($active_session_id)) {
            if (!empty($sessions)) {
                $active_session_id = $sessions[0]['id'];
            } else {
                // Initialize default conversation session
                $this->db->insert('ai_chat_sessions', array(
                    'title' => 'New Conversation',
                    'is_starred' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'user_id' => $this->user_id
                ));
                $active_session_id = $this->db->insert_id();
                // Re-fetch sessions
                $sessions = $this->db->where('user_id', $this->user_id)->order_by('is_starred', 'DESC')->order_by('id', 'DESC')->get('ai_chat_sessions')->result_array();
            }
        }
        
        $data['sessions'] = $sessions;
        $data['active_session_id'] = $active_session_id;
        
        // Fetch message logs for active session
        $data['chat_messages'] = $this->db->where('session_id_fk', $active_session_id)->order_by('id', 'ASC')->get('ai_chat_messages')->result_array();

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('ai_insights/chat', $data);
    }

    /**
     * Settings View
     * URL: AiController/settings
     */
    public function settings()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        
        // Fetch AI Settings
        $settings_rows = $this->db->get('ai_settings')->result_array();
        $ai_settings = array();
        foreach ($settings_rows as $row) {
            $ai_settings[$row['setting_key']] = $row['setting_value'];
        }
        $data['ai_settings'] = $ai_settings;
        $data['settings'] = $this->login->get_settings($this->user_id);

        // Fetch AI Governance Logs joined with BOM number
        $data['governance_logs'] = $this->db->select('g.*, b.number_fk as bom_number')
                                            ->from('ai_governance_log g')
                                            ->join('bom_total b', 'b.id = g.record_id AND b.uid = g.user_id', 'left')
                                            ->order_by('g.id', 'DESC')
                                            ->limit(100)
                                            ->get()
                                            ->result_array();

        // Fetch AI Learned Memories
        $data['learned_memories'] = $this->db->where('user_id', $this->user_id)
                                             ->order_by('id', 'DESC')
                                             ->get('ai_memory')
                                             ->result_array();

        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('ai_insights/settings', $data);
    }

    /**
     * AJAX endpoint: Delete learned memory
     * URL: AiController/ajax_delete_memory
     */
    public function ajax_delete_memory()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id');
        if (empty($id)) {
            echo json_encode(array('success' => false, 'message' => 'Memory ID is required.'));
            return;
        }

        $this->db->where('id', $id)->where('user_id', $this->user_id)->delete('ai_memory');
        echo json_encode(array('success' => true, 'message' => 'Memory fact removed from system database successfully.'));
    }

    /**
     * AJAX endpoint: Update AI settings
     */
    public function ajax_save_settings()
    {
        header('Content-Type: application/json');
        
        $api_key = $this->input->post('groq_api_key');
        $model = $this->input->post('groq_model');
        $expiry = $this->input->post('cache_expiry');

        if (empty($api_key) || empty($model) || empty($expiry)) {
            echo json_encode(array('success' => false, 'message' => 'All settings fields are required.'));
            return;
        }

        $this->db->where('setting_key', 'groq_api_key')->update('ai_settings', array('setting_value' => $api_key));
        $this->db->where('setting_key', 'groq_model')->update('ai_settings', array('setting_value' => $model));
        $this->db->where('setting_key', 'cache_expiry')->update('ai_settings', array('setting_value' => $expiry));

        echo json_encode(array('success' => true, 'message' => 'Settings saved successfully.'));
    }

    /**
     * AJAX endpoint: Get custom AI Insight for a single BOM with caching
     */
    public function ajax_get_bom_ai_insight()
    {
        header('Content-Type: application/json');
        $bom_id = $this->input->post('bom_id');
        if (empty($bom_id)) {
            echo json_encode(array('success' => false, 'message' => 'BOM ID is required.'));
            return;
        }

        $prefix = $this->db->dbprefix;
        $bom = $this->db->select('bt.*, c.company_name, c.fullname,
                                  (SELECT COUNT(*) FROM ' . $prefix . 'bom b WHERE b.number = bt.number_fk AND b.uid = bt.uid) as item_count')
                        ->from('bom_total bt')
                        ->join('customer c', 'c.customer_id = bt.customer_id_fk', 'left')
                        ->where('bt.id', $bom_id)
                        ->where('bt.uid', $this->user_id)
                        ->get()
                        ->row_array();

        if (!$bom) {
            echo json_encode(array('success' => false, 'message' => 'BOM not found.'));
            return;
        }

        // Fetch top 10 items in this BOM along with current inventory stock levels
        $items = $this->db->select('b.product_name, b.quantity, b.description, b.unit, i.available_stock')
                          ->from('bom b')
                          ->join('inventory i', 'i.code = b.product_name AND i.uid = b.uid', 'left')
                          ->where('b.number', $bom['number_fk'])
                          ->where('b.uid', $this->user_id)
                          ->limit(10)
                          ->get()
                          ->result_array();

        $components_info = "";
        if (!empty($items)) {
            $components_info .= "Component Items & Current Stock:\n";
            foreach ($items as $item) {
                $stock = isset($item['available_stock']) ? floatval($item['available_stock']) : 0;
                $req = floatval($item['quantity']);
                $shortage_calc = max(0, $req - $stock);
                $components_info .= "- " . ($item['product_name'] ?: 'Unknown Code') . " (" . ($item['description'] ?: 'No Description') . "): Required: {$req}, Stock Available: {$stock} (Shortage: {$shortage_calc})\n";
            }
        } else {
            $components_info .= "Components: No items added to this BOM yet.\n";
        }

        // Gather details
        $days_stale = 0;
        if (!empty($bom['date']) && $bom['date'] !== '0000-00-00') {
            $bom_time = strtotime($bom['date']);
            if ($bom_time > 0) {
                $days_stale = floor((time() - $bom_time) / (60 * 60 * 24));
            }
        }
        $customer = !empty($bom['company_name']) ? $bom['company_name'] : ($bom['fullname'] ?? 'Unassigned');
        $mrp_status = ($bom['send_to_mrp'] == 2) ? 'Yes (Anomalous Run)' : 'No';
        
        $prompt = "Provide a custom triage analysis for the following Draft BOM:\n" .
                  "- BOM Number: {$bom['number_fk']}\n" .
                  "- Customer: {$customer}\n" .
                  "- Target System: " . ($bom['system'] ?: 'Blank') . "\n" .
                  "- Component Items Count: {$bom['item_count']} items\n" .
                  "- Days since creation: {$days_stale} days\n" .
                  "- MRP Executed: {$mrp_status}\n\n" .
                  $components_info . "\n" .
                  "Write exactly 2 sentences. First sentence: state the roadblock explaining why it is stalled (be specific if there are component shortages or missing descriptors). Second sentence: give a direct, friendly instruction telling the engineering user exactly what action to take next.";

        // Cache checking mechanism
        $source_hash = hash('sha256', $prompt);
        $cached_insight = $this->get_cached_insight('bom', $bom_id, $source_hash);
        
        // Agentic flag check: Can be submitted for approval if has items, customer, and system name
        $can_submit = ($bom['item_count'] > 0 && !empty($bom['system']) && $bom['status'] == 1);

        if ($cached_insight) {
            echo json_encode(array(
                'success' => true, 
                'insight' => $cached_insight, 
                'cached' => true,
                'can_submit' => $can_submit,
                'bom_id' => $bom_id
            ));
            return;
        }

        // Call Groq AI and store to cache
        $insight = $this->call_groq_ai($prompt);
        $this->save_cached_insight('bom', $bom_id, $insight, $source_hash);
        
        echo json_encode(array(
            'success' => true, 
            'insight' => $insight, 
            'cached' => false,
            'can_submit' => $can_submit,
            'bom_id' => $bom_id
        ));
    }

    /**
     * AJAX endpoint: Execute AI recommended action (e.g. submit BOM for approval)
     * URL: AiController/ajax_execute_agentic_action
     */
    public function ajax_execute_agentic_action()
    {
        header('Content-Type: application/json');
        $bom_id = $this->input->post('bom_id');
        $action = $this->input->post('action');
        $rec = $this->input->post('recommendation');

        if (empty($bom_id) || empty($action)) {
            echo json_encode(array('success' => false, 'message' => 'BOM ID and Action are required.'));
            return;
        }

        if ($action === 'submit_bom_for_approval') {
            $bom_total = $this->db->where('id', $bom_id)->where('uid', $this->user_id)->get('bom_total')->row();
            if (!$bom_total) {
                echo json_encode(array('success' => false, 'message' => 'BOM not found.'));
                return;
            }

            // Execute the action: submit for approval (set status to 0 - Pending)
            $this->db->where('id', $bom_id)
                     ->where('uid', $this->user_id)
                     ->update('bom_total', array('status' => 0));

            // Log this to the governance log table
            $this->db->insert('ai_governance_log', array(
                'action_type' => 'Submit BOM for Approval',
                'module' => 'bom',
                'record_id' => $bom_id,
                'recommendation_text' => $rec ?: 'AI Triage: Submit BOM ' . $bom_total->number_fk . ' for approval.',
                'human_decision' => 'approved',
                'executed_at' => date('Y-m-d H:i:s'),
                'user_id' => $this->user_id
            ));

            // Auto-train/Learn this action by inserting it into the ai_memory table
            $this->db->insert('ai_memory', array(
                'memory_type' => 'user_action',
                'context_key' => 'bom_approval',
                'learned_fact' => 'User Shivansh approved and submitted BOM ' . $bom_total->number_fk . ' for approval on ' . date('Y-m-d H:i:s') . ' after AI triage suggested: ' . ($rec ?: 'Submit BOM for approval.'),
                'created_at' => date('Y-m-d H:i:s'),
                'user_id' => $this->user_id
            ));

            echo json_encode(array('success' => true, 'message' => 'BOM ' . $bom_total->number_fk . ' successfully submitted for approval. Action logged to AI Governance Table and dynamic memory database.'));
            return;
        }

        echo json_encode(array('success' => false, 'message' => 'Action type not supported.'));
    }

    /**
     * AJAX endpoint: Log AI recommended action rejection
     * URL: AiController/ajax_reject_agentic_action
     */
    public function ajax_reject_agentic_action()
    {
        header('Content-Type: application/json');
        $bom_id = $this->input->post('bom_id');
        $rec = $this->input->post('recommendation');

        if (empty($bom_id)) {
            echo json_encode(array('success' => false, 'message' => 'BOM ID is required.'));
            return;
        }

        $bom_total = $this->db->where('id', $bom_id)->where('uid', $this->user_id)->get('bom_total')->row();

        // Log this to the governance log table
        $this->db->insert('ai_governance_log', array(
            'action_type' => 'Submit BOM for Approval',
            'module' => 'bom',
            'record_id' => $bom_id,
            'recommendation_text' => $rec ?: 'AI Triage recommendation.',
            'human_decision' => 'rejected',
            'executed_at' => date('Y-m-d H:i:s'),
            'user_id' => $this->user_id
        ));

        if ($bom_total) {
            // Auto-train/Learn this action by inserting it into the ai_memory table
            $this->db->insert('ai_memory', array(
                'memory_type' => 'user_action',
                'context_key' => 'bom_rejection',
                'learned_fact' => 'User Shivansh rejected and dismissed the recommendation to submit BOM ' . $bom_total->number_fk . ' for approval on ' . date('Y-m-d H:i:s') . ' (AI triage suggestion was: ' . ($rec ?: 'Submit BOM for approval.') . ').',
                'created_at' => date('Y-m-d H:i:s'),
                'user_id' => $this->user_id
            ));
        }

        echo json_encode(array('success' => true, 'message' => 'Recommendation dismissed. Rejection logged to AI Governance Table and dynamic memory database.'));
    }

    /**
     * AJAX endpoint: Chat bot message processor
     * URL: AiController/ajax_chat_message
     */
    public function ajax_chat_message()
    {
        header('Content-Type: application/json');
        $message = $this->input->post('message');
        $session_id = $this->input->post('session_id');
        
        if (empty($message) || empty($session_id)) {
            echo json_encode(array('success' => false, 'message' => 'Message and Session ID are required.'));
            return;
        }

        // Save User Message to Database
        $this->db->insert('ai_chat_messages', array(
            'session_id_fk' => $session_id,
            'sender' => 'user',
            'message_text' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ));

        // Auto-rename session if it is currently default "New Conversation"
        $session = $this->db->where('id', $session_id)->get('ai_chat_sessions')->row_array();
        if ($session && $session['title'] === 'New Conversation') {
            $new_title = substr(strip_tags($message), 0, 30);
            if (strlen($message) > 30) $new_title .= '...';
            $this->db->where('id', $session_id)->update('ai_chat_sessions', array('title' => $new_title));
        }

        // Smart Intent Parsing
        $reply = "";
        $message_lower = strtolower($message);

        // Check if user is teaching the AI (Auto-training from chat)
        $is_learning_intent = false;
        $fact_to_learn = '';
        $prefixes = array('remember that', 'remember', 'always assume', 'always', 'note that', 'actually');
        foreach ($prefixes as $pref) {
            if (stripos(trim($message_lower), $pref) === 0) {
                $is_learning_intent = true;
                $fact_to_learn = substr($message, strlen($pref));
                $fact_to_learn = trim(ltrim($fact_to_learn, ':,- '));
                break;
            }
        }

        if ($is_learning_intent && !empty($fact_to_learn)) {
            // Save learned fact to memory table for auto-training
            $this->db->insert('ai_memory', array(
                'memory_type' => 'user_preference',
                'context_key' => 'user_chat_instruction',
                'learned_fact' => $fact_to_learn,
                'created_at' => date('Y-m-d H:i:s'),
                'user_id' => $this->user_id
            ));

            echo json_encode(array(
                'success' => true, 
                'reply' => "🤖 **Memory Recorded & Learned!**\n\nI have saved this rule to my training memory database: *\"{$fact_to_learn}\"*.\nI will automatically inject and apply this context to all future chatbot responses and triage analyses."
            ));
            return;
        }

        // 1. Check for Sales Order (SO) lookup intent
        $matched_so = '';
        if (preg_match('/(XFORM-[A-Z0-9-]+-OC-\d+|XFORM-[A-Z0-9-]+|\bOC[-\s]?\d+\b)/i', $message, $matches)) {
            $matched_so = trim($matches[1]);
        }
        if (empty($matched_so) && (strpos($message_lower, 'so') !== false || strpos($message_lower, 'sales') !== false || strpos($message_lower, 'order') !== false)) {
            if (preg_match('/\b(\d{3,5})\b/', $message, $matches)) {
                $matched_so = $matches[1];
            }
        }

        // Regex match: BOM/00149/26-27, BOM/00149, or raw 5-digit sequence 00149
        $matched_bom = '';
        if (preg_match('/(BOM\/\d+(?:\/\d+-\d+)?|\b\d{5}\b)/i', $message, $matches)) {
            $matched_bom = $matches[1];
        }

        if (!empty($matched_so)) {
            // Clean up prefix (e.g. if matched OC-129, query for '129')
            $clean_so = preg_replace('/^OC[-\s]?/i', '', $matched_so);
            
            $prefix = $this->db->dbprefix;
            $so_row = $this->db->select('st.*, c.company_name, c.fullname, u.username')
                               ->from('salesorder_total st')
                               ->join('customer c', 'c.customer_id = st.customer_id_fk', 'left')
                               ->join('user u', 'u.user_id = st.uid', 'left')
                               ->group_start()
                                   ->where('st.number_fk', $matched_so)
                                   ->or_where('st.oc_number', $matched_so)
                                   ->or_where('st.oc_number', $clean_so)
                                   ->or_like('st.number_fk', $matched_so, 'both')
                               ->group_end()
                               ->get()
                               ->row_array();

            if (!$so_row) {
                $reply = "I searched the database but could not find a Sales Order matching **" . htmlspecialchars($matched_so) . "**. Please check the number and try again.";
            } else {
                $customer_name = !empty($so_row['company_name']) ? $so_row['company_name'] : ($so_row['fullname'] ?? 'Unassigned');
                $status_lbl = 'Pending';
                if ($so_row['status'] == 1) $status_lbl = 'Approved';
                elseif ($so_row['status'] == 2) $status_lbl = 'Canceled';
                
                $prompt = "You must ONLY use the provided real data for this Sales Order. Do not invent any other creator names, dates, or statuses. If details are blank, say 'Not Available'.\n\n" .
                          "Sales Order Data:\n" .
                          "- Sales Order Number: {$so_row['number_fk']}\n" .
                          "- OC Number: " . ($so_row['oc_number'] ?: 'Not Available') . "\n" .
                          "- Created By: " . ($so_row['username'] ?: 'System / Unknown') . " (User ID: {$so_row['uid']})\n" .
                          "- Date Created: " . ($so_row['date'] ?: 'Not Available') . "\n" .
                          "- Customer: {$customer_name}\n" .
                          "- Project Code: " . ($so_row['project_code'] ?: 'Not Specified') . "\n" .
                          "- Total Value: " . number_format($so_row['total'], 2) . "\n\n" .
                          "Answer the user's question directly and concisely in 2-3 sentences based strictly on the facts above.";

                $reply = $this->call_groq_ai($prompt);
            }
        } elseif (!empty($matched_bom)) {
            // Secure grounded lookup
            $prefix = $this->db->dbprefix;
            $bom_query = $this->db->select('bt.*, c.company_name, c.fullname')
                                  ->from('bom_total bt')
                                  ->join('customer c', 'c.customer_id = bt.customer_id_fk', 'left')
                                  ->where('bt.uid', $this->user_id);
                                  
            if (stripos($matched_bom, 'BOM/') === 0) {
                $bom_query->where('bt.number_fk', $matched_bom);
            } else {
                $bom_query->like('bt.number_fk', $matched_bom, 'both');
            }
            
            $bom_row = $bom_query->get()->row_array();

            if (!$bom_row) {
                $reply = "I searched the database but could not find a Bill of Materials matching **" . htmlspecialchars($matched_bom) . "**. Please check the BOM number and try again.";
            } else {
                // Fetch actual component items
                $components = $this->db->select('b.product_name, b.quantity, b.description, b.unit, i.available_stock')
                                       ->from('bom b')
                                       ->join('inventory i', 'i.code = b.product_name AND i.uid = b.uid', 'left')
                                       ->where('b.number', $bom_row['number_fk'])
                                       ->where('b.uid', $this->user_id)
                                       ->limit(10)
                                       ->get()
                                       ->result_array();

                // Format status labels
                $status_label = 'Pending';
                $status_val = (int)$bom_row['status'];
                if ($status_val == 1) $status_label = 'Draft';
                elseif ($status_val == 2) $status_label = 'Sent';
                elseif ($status_val == 3) $status_label = 'Viewed';
                elseif ($status_val == 4) $status_label = 'Approved';
                elseif ($status_val == 5) $status_label = 'Rejected';
                elseif ($status_val == 6) $status_label = 'Canceled';
                elseif ($status_val == 7) $status_label = 'Under Review';

                $components_text = "";
                if (!empty($components)) {
                    foreach ($components as $c) {
                        $stock = isset($c['available_stock']) ? floatval($c['available_stock']) : 0;
                        $components_text .= "- " . ($c['product_name'] ?: 'Unknown Code') . ": Required: " . $c['quantity'] . ", Stock: " . $stock . " (" . ($c['description'] ?: 'No desc') . ")\n";
                    }
                } else {
                    $components_text = "No components added yet.\n";
                }

                $prompt = "You must ONLY use the provided real data for this BOM. Do not invent any other components, dates, statuses, or machining percentages. If information is missing, explicitly say 'Not Available' — do not guess.\n\n" .
                          "BOM Data:\n" .
                          "- BOM Number: {$bom_row['number_fk']}\n" .
                          "- Customer: " . (!empty($bom_row['company_name']) ? $bom_row['company_name'] : ($bom_row['fullname'] ?? 'Unassigned')) . "\n" .
                          "- Target Assembly/System: " . ($bom_row['system'] ?: 'Not Specified') . "\n" .
                          "- Current Status: {$status_label}\n" .
                          "- Date: " . ($bom_row['date'] ?: 'Not Available') . "\n" .
                          "- MRP Status: " . (($bom_row['send_to_mrp'] == 2) ? 'MRP Run executed' : (($bom_row['send_to_mrp'] == 1) ? 'Sent to MRP' : 'Not sent')) . "\n\n" .
                          "Components list:\n" . $components_text . "\n" .
                          "Summarize this BOM status and components list factually in 3-4 lines.";

                $reply = $this->call_groq_ai($prompt);
            }
        } elseif (strpos($message_lower, 'duplicate') !== false || strpos($message_lower, 'conflict') !== false) {
            // Find duplicate Draft BOM systems
            $prefix = $this->db->dbprefix;
            $sql = "SELECT DISTINCT bt1.system, bt1.number_fk as draft_bom, bt2.number_fk as approved_bom
                    FROM {$prefix}bom_total bt1
                    JOIN {$prefix}bom_total bt2 ON bt2.system = bt1.system AND bt2.status = 4 AND bt2.uid = bt1.uid
                    WHERE bt1.status = 1 AND bt1.uid = {$this->user_id} AND bt1.system != ''";
            $duplicates = $this->db->query($sql)->result_array();

            if (empty($duplicates)) {
                $reply = "I scanned the database and did not find any duplicate Draft BOMs for already approved systems.";
            } else {
                $details = "I found the following conflicting/duplicate BOM systems:\n";
                foreach ($duplicates as $d) {
                    $details .= "- System *{$d['system']}*: Draft BOM `{$d['draft_bom']}` conflicts with Approved BOM `{$d['approved_bom']}`.\n";
                }
                $prompt = "State that you found duplicate BOM entries and explain why this is an issue. Formulate a short recommendation based on this live database data:\n" . $details;
                $reply = $this->call_groq_ai($prompt);
            }
        } elseif (strpos($message_lower, 'anomaly') !== false || strpos($message_lower, 'anomalies') !== false || strpos($message_lower, 'mrp') !== false) {
            // Find BOMs with premature MRP runs
            $prefix = $this->db->dbprefix;
            $sql = "SELECT number_fk, system FROM {$prefix}bom_total WHERE status = 1 AND send_to_mrp = 2 AND uid = {$this->user_id}";
            $anomalies = $this->db->query($sql)->result_array();

            if (empty($anomalies)) {
                $reply = "I checked the database. Excellent! No Draft BOMs have had MRP runs run against them prematurely.";
            } else {
                $count = count($anomalies);
                $details = "I detected {$count} Draft BOMs that have had MRP runs executed without approval:\n";
                foreach (array_slice($anomalies, 0, 5) as $a) {
                    $details .= "- BOM `{$a['number_fk']}` for system: {$a['system']}\n";
                }
                $prompt = "Explain that there are unapproved drafts with active MRP runs in the factory. Give an instruction based on this data:\n" . $details;
                $reply = $this->call_groq_ai($prompt);
            }
        } elseif (strpos($message_lower, 'stale') !== false || strpos($message_lower, 'old') !== false || strpos($message_lower, 'stuck') !== false) {
            // Find oldest Draft BOMs
            $prefix = $this->db->dbprefix;
            $sql = "SELECT number_fk, date, system FROM {$prefix}bom_total WHERE status = 1 AND uid = {$this->user_id} ORDER BY date ASC LIMIT 5";
            $oldest = $this->db->query($sql)->result_array();

            if (empty($oldest)) {
                $reply = "There are no pending Draft BOMs currently in the system.";
            } else {
                $details = "Here are the oldest pending Draft BOMs:\n";
                foreach ($oldest as $o) {
                    $days = floor((time() - strtotime($o['date'])) / (60 * 60 * 24));
                    if ($days < 0 || $o['date'] == '0000-00-00') $days = 'unknown';
                    $details .= "- BOM `{$o['number_fk']}` ({$days} days old) for assembly: " . ($o['system'] ?: 'unspecified') . "\n";
                }
                $prompt = "Review these stale Draft BOMs and write a brief notice for the procurement manager to nudge the team:\n" . $details;
                $reply = $this->call_groq_ai($prompt);
            }
        } else {
            // General conversation/help fallback
            $prompt = "Answer the user question about the factory or manufacturing ERP context. User says: \"{$message}\"";
            $reply = $this->call_groq_ai($prompt);
        }

        // Save AI Response to Database
        $this->db->insert('ai_chat_messages', array(
            'session_id_fk' => $session_id,
            'sender' => 'ai',
            'message_text' => $reply,
            'created_at' => date('Y-m-d H:i:s')
        ));

        echo json_encode(array('success' => true, 'reply' => $reply));
    }

    /**
     * Retrieve cached insight
     */
    private function get_cached_insight($module, $record_id, $source_hash)
    {
        $prefix = $this->db->dbprefix;
        $row = $this->db->where('module', $module)
                        ->where('record_id', $record_id)
                        ->where('source_hash', $source_hash)
                        ->get('ai_insights_cache')
                        ->row_array();
        if ($row) {
            $cache_expiry_hours = 24;
            $expiry_setting = $this->db->where('setting_key', 'cache_expiry')->get('ai_settings')->row_array();
            if ($expiry_setting) {
                $cache_expiry_hours = (int)$expiry_setting['setting_value'];
            }
            
            $generated_time = strtotime($row['generated_at']);
            if (time() - $generated_time < ($cache_expiry_hours * 3600)) {
                return $row['insight_text'];
            }
        }
        return null;
    }

    /**
     * Save insight to cache
     */
    private function save_cached_insight($module, $record_id, $insight_text, $source_hash)
    {
        $prefix = $this->db->dbprefix;
        $this->db->where('module', $module)->where('record_id', $record_id)->delete('ai_insights_cache');
        
        $this->db->insert('ai_insights_cache', array(
            'module' => $module,
            'record_id' => $record_id,
            'insight_text' => $insight_text,
            'generated_at' => date('Y-m-d H:i:s'),
            'source_hash' => $source_hash
        ));
    }

    /**
     * Internal helper to query Groq LLM API
     */
    private function call_groq_ai($prompt)
    {
        // Query setting value
        $key_setting = $this->db->where('setting_key', 'groq_api_key')->get('ai_settings')->row_array();
        $model_setting = $this->db->where('setting_key', 'groq_model')->get('ai_settings')->row_array();

        $api_key = $key_setting ? $key_setting['setting_value'] : '';
        $model = $model_setting ? $model_setting['setting_value'] : 'llama-3.1-8b-instant';

        // Retrieve dynamic learned memories for in-context learning
        $memories = $this->db->where('user_id', $this->user_id)->limit(15)->get('ai_memory')->result_array();
        $memory_context = "";
        if (!empty($memories)) {
            $memory_context .= "\n\nCRITICAL - LEARNED WORKFLOW PREFERENCES / MEMORIES (AUTO-TRAINED RULES):\n";
            foreach ($memories as $m) {
                $memory_context .= "- Fact/Preference: " . $m['learned_fact'] . " (Learned on " . date('Y-m-d H:i', strtotime($m['created_at'])) . ")\n";
            }
            $memory_context .= "Respect these user-trained preferences and apply them strictly to your analyses or chat answers if they relate to the target records or concepts.";
        }

        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $data = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'You are an AI assistant built into a manufacturing ERP. Be concise, actionable, and focus directly on the manufacturing data. Avoid verbose greetings or fluff. Never invent facts, component names, dates, or statuses that are not explicitly provided to you.' . $memory_context
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.2,
            'max_tokens' => 300
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return 'AI Insights: Connection to Groq API timed out.';
        }
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? 'AI Insights: Please review the parameters manually.';
    }

    /**
     * AJAX endpoint: Create a new chat session
     * URL: AiController/ajax_new_chat_session
     */
    public function ajax_new_chat_session()
    {
        header('Content-Type: application/json');
        
        $this->db->insert('ai_chat_sessions', array(
            'title' => 'New Conversation',
            'is_starred' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'user_id' => $this->user_id
        ));
        $new_id = $this->db->insert_id();
        
        echo json_encode(array('success' => true, 'session_id' => $new_id));
    }

    /**
     * AJAX endpoint: Rename a chat session
     * URL: AiController/ajax_rename_chat_session
     */
    public function ajax_rename_chat_session()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        
        if (empty($id) || empty($title)) {
            echo json_encode(array('success' => false, 'message' => 'ID and Title are required.'));
            return;
        }

        $this->db->where('id', $id)
                 ->where('user_id', $this->user_id)
                 ->update('ai_chat_sessions', array('title' => $title));

        echo json_encode(array('success' => true));
    }

    /**
     * AJAX endpoint: Star / Unstar a chat session
     * URL: AiController/ajax_star_chat_session
     */
    public function ajax_star_chat_session()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id');
        
        if (empty($id)) {
            echo json_encode(array('success' => false, 'message' => 'ID is required.'));
            return;
        }

        $session = $this->db->where('id', $id)->where('user_id', $this->user_id)->get('ai_chat_sessions')->row_array();
        if ($session) {
            $new_star = $session['is_starred'] ? 0 : 1;
            $this->db->where('id', $id)->update('ai_chat_sessions', array('is_starred' => $new_star));
            echo json_encode(array('success' => true, 'is_starred' => $new_star));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Session not found.'));
        }
    }

    /**
     * AJAX endpoint: Delete a chat session and its message logs
     * URL: AiController/ajax_delete_chat_session
     */
    public function ajax_delete_chat_session()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id');
        
        if (empty($id)) {
            echo json_encode(array('success' => false, 'message' => 'ID is required.'));
            return;
        }

        $this->db->where('id', $id)->where('user_id', $this->user_id)->delete('ai_chat_sessions');
        $this->db->where('session_id_fk', $id)->delete('ai_chat_messages');
        
        echo json_encode(array('success' => true));
    }
}
