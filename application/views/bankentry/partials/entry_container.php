<?php
// Entry container partial (AJAX injected into Bank Entry page)
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style>
    /* limit styles to this container */
    #bank-entry-container .bank-entry-ajax-loading {
        padding: 20px;
        color: #666;
        font-weight: 600;
        text-align: center;
    }
</style>

<div id="bank-entry-container" data-current="" >
    <div class="bank-entry-ajax-loading">Select entry type to continue...</div>
</div>

