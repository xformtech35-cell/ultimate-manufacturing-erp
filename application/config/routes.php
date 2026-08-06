<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Add these routes for ApprovalMatrixController
$route['ApprovalMatrix'] = 'ApprovalMatrixController/index';
$route['ApprovalMatrix/add'] = 'ApprovalMatrixController/add';
$route['ApprovalMatrix/edit/(:num)'] = 'ApprovalMatrixController/edit/$1';
$route['ApprovalMatrix/delete/(:num)'] = 'ApprovalMatrixController/delete/$1';

// User routes
$route['users'] = 'UserController/index';
$route['users/add'] = 'UserController/add_user_form';
$route['users/edit/(:num)'] = 'UserController/get_user_by_id/$1';
$route['users/delete/(:num)'] = 'UserController/delete_user_by_id/$1';
$route['users/import'] = 'UserController/import_form';
$route['users/export/excel'] = 'UserController/export_excel';
$route['users/export/csv'] = 'UserController/export_csv';
$route['users/download/template'] = 'UserController/download_template';


$route['supplier/export'] = 'SupplierController/export_vendors';
$route['supplier/export-pdf'] = 'SupplierController/export_vendors_pdf';
$route['supplier/import'] = 'SupplierController/import_vendors_view';
$route['supplier/import-template'] = 'SupplierController/download_vendor_template';
$route['supplier/import-process'] = 'SupplierController/process_vendor_import';

// Add to routes.php
$route['grn-approvals'] = 'GrnController/grn_approvals';
$route['process-grn-approval'] = 'GrnController/process_grn_approval';
$route['grn-approval-details/(:any)'] = 'GrnController/show_grn_approval_details/$1';

// Order Confirmation Routes
$route['OrderConfirmation'] = 'OrderConfirmationController/index';
$route['OrderConfirmation/create'] = 'OrderConfirmationController/create_order_confirmation';
$route['OrderConfirmation/show/(:any)'] = 'OrderConfirmationController/show_order_confirmation/$1';
$route['OrderConfirmation/edit/(:any)'] = 'OrderConfirmationController/edit_order_confirmation_details/$1';
$route['OrderConfirmation/save'] = 'OrderConfirmationController/save_order_confirmation';
$route['OrderConfirmation/update'] = 'OrderConfirmationController/update_order_confirmation';
$route['OrderConfirmation/delete/(:any)'] = 'OrderConfirmationController/delete_order_confirmation/$1';
$route['OrderConfirmation/update-status/(:any)/(:num)'] = 'OrderConfirmationController/update_status/$1/$2';
$route['OrderConfirmation/print/(:any)'] = 'OrderConfirmationController/print_order_confirmation/$1';
$route['OrderConfirmation/create-from-so/(:any)'] = 'OrderConfirmationController/create_from_so/$1';
$route['OrderConfirmation/print-oa-letter/(:any)'] = 'OrderConfirmationController/print_oa_letter/$1';

// JobOrder Routes
$route['JobOrder'] = 'JobOrderController/index';
$route['JobOrder/create'] = 'JobOrderController/create_job_order';
$route['JobOrder/save'] = 'JobOrderController/save_job_order';
$route['JobOrder/show/(:any)'] = 'JobOrderController/show_job_order/$1';
$route['JobOrder/edit/(:any)'] = 'JobOrderController/edit_job_order_details/$1';
$route['JobOrder/delete/(:any)'] = 'JobOrderController/delete_job_order/$1';
$route['JobOrder/print/(:any)'] = 'JobOrderController/print_job_order/$1';
$route['JobOrder/update-status/(:any)/(:num)'] = 'JobOrderController/update_status/$1/$2';
$route['JobOrder/add_customer'] = 'JobOrderController/add_customer';


$route['add-project'] = 'ProjectController/index';
$route['project/add'] = 'ProjectController/add_project';
$route['project/edit/(:num)'] = 'ProjectController/edit_project/$1';
$route['project/update'] = 'ProjectController/update_project';
$route['project/delete/(:num)'] = 'ProjectController/delete_project/$1';

// PurchaseController Route Aliases
$route['PurchaseController/create'] = 'SupplierController/create_purchase_order';
$route['PurchaseController/create_purchase_order'] = 'SupplierController/create_purchase_order';
$route['PurchaseController/(:any)'] = 'SupplierController/$1';
$route['PurchaseController'] = 'SupplierController/view_purchase_order';