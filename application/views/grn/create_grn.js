$(document).ready(function() {
    // Initialize datepickers
    $('.alldate').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
    });
    
    // PO change - load items
    $('#po_number').change(function() {
        var po_number = $(this).val();
        if (po_number) {
            var baseGrn = $('#grn_number').val().split('/(')[0];
            var match = po_number.match(/\/\(([0-9]+\/[0-9]+)\)$/);
            if (match) {
                baseGrn += '/(' + match[1] + ')';
            }
            $('#grn_number').val(baseGrn);
            $('label[name="number"] h2 b').text(' ' + baseGrn + ' ');

            $.post('<?php echo base_url(); ?>GrnController/get_po_details_details', {po_number: po_number}, function(data) {
                if (data && data.length > 0) {
                    $('#dynamic_field tbody').empty();
                    $.each(data, function(index, item) {
                        addRow(item);
                    });
                    calculateTotals();
                }
            }, 'json');
        }
    });
    
    // Amount to words function
    function amountToWords(amount) {
        var words = new Array();
        words[0] = '';
        words[1] = 'One';
        words[2] = 'Two';
        words[3] = 'Three';
        words[4] = 'Four';
        words[5] = 'Five';
        words[6] = 'Six';
        words[7] = 'Seven';
        words[8] = 'Eight';
        words[9] = 'Nine';
        words[10] = 'Ten';
        words[11] = 'Eleven';
        words[12] = 'Twelve';
        words[13] = 'Thirteen';
        words[14] = 'Fourteen';
        words[15] = 'Fifteen';
        words[16] = 'Sixteen';
        words[17] = 'Seventeen';
        words[18] = 'Eighteen';
        words[19] = 'Nineteen';
        words[20] = 'Twenty';
        words[30] = 'Thirty';
        words[40] = 'Forty';
        words[50] = 'Fifty';
        words[60] = 'Sixty';
        words[70] = 'Seventy';
        words[80] = 'Eighty';
        words[90] = 'Ninety';
        var th = ['', 'Thousand', 'Lakh', 'Crore'];
        
        amount = Math.floor(amount);
        var atemp = amount.toString().split('').reverse().join('').split('').reverse();
        var i = 0;
        var str = '';
        var n = 0;
        while (i < atemp.length) {
            n = '';
            if ((i % 3) == 0) n = atemp[i + 2] || '', n += atemp[i + 1] || '', n += atemp[i] || '';
            if (n != '') {
                str += (words[parseInt(n)] || words[parseInt(n[0])] + ' ' + words[parseInt(n[1])] + ' ' + words[parseInt(n[2])]) + ' ' + th[Math.floor(i / 3)] + ' ';
            }
            i += 3;
        }
        return (str.replace(/  +/g, ' ').replace(/ $/g, '') || 'Zero') + ' Rupees Only';
    }
    
    function updateAmountInWords() {
        var grandTotal = parseFloat($('#total_quotation_amount').val()) || 0;
        $('#amount_in_words').text(amountToWords(grandTotal));
    }
    
    function calculateRow(row) {
        var received = parseFloat(row.find('.received_quantity').val()) || 0;
        var price = parseFloat(row.find('.price').val()) || 0;
        var subtotal = received * price;
        var sgst_rate = parseFloat(row.find('.sgst_rate').val()) || 0;
        var cgst_rate = parseFloat(row.find('.cgst_rate').val()) || 0;
        row.find('.subtotal').val(subtotal.toFixed(2));
        var sgst_amt = (subtotal * sgst_rate / 100);
        var cgst_amt = (subtotal * cgst_rate / 100);
        row.find('.sgst_amount').val(sgst_amt.toFixed(2));
        row.find('.cgst_amount').val(cgst_amt.toFixed(2));
        var row_total = subtotal + sgst_amt + cgst_amt;
        row.find('.amount').text('₹' + row_total.toFixed(2));
        return row_total;
    }
    
function calculateTotals() {
        var basic_total = 0;
        var total_sgst = 0;
        var total_cgst = 0;
        var grand_total = 0;
        
        $('#dynamic_field tbody tr').each(function() {
            var row_total = calculateRow($(this));
            basic_total += parseFloat($(this).find('.subtotal').val()) || 0;
            total_sgst += parseFloat($(this).find('.sgst_amount').val()) || 0;
            total_cgst += parseFloat($(this).find('.cgst_amount').val()) || 0;
            grand_total += row_total;
        });
        
        $('#total_grn_amount1').val(basic_total.toFixed(2));
        $('#total_quotation_amount').val(grand_total.toFixed(2));
        
        $('#basic_total_display').text('Basic Total: ₹' + basic_total.toFixed(2));
        $('#sgst_amount').text('SGST Amount: ₹' + total_sgst.toFixed(2));
        $('#cgst_amount').text('CGST Amount: ₹' + total_cgst.toFixed(2));
        $('#grand_total_amount').text('Grand Total: ₹' + grand_total.toFixed(2));
        
        updateAmountInWords();
    }
    
function addRow(item) {
        var quantity = parseFloat(item.quantity || 0);
        var received_quantity = parseFloat(item.received_quantity || 0);
        var pending_qty = Math.max(0, quantity - received_quantity);
        var row = $('<tr>');
        row.html(`
            <td><input type="text" name="term[]" class="form-control item_name" value="${item.product_name || ''}" readonly /></td>
            <td><input type="text" name="description[]" class="form-control description" value="${item.description || ''}" /></td>
            <td><input type="number" name="quantity[]" class="form-control quantity" value="${quantity}" readonly /></td>
            <td><input type="text" name="hsn[]" class="form-control hsn" value="${item.hsn_code || ''}" /></td>
            <td><input type="number" step="0.01" name="gst_per[]" class="form-control gst_rate" value="${item.gst || 0}" /></td>
            <td><input type="number" step="0.01" name="sgst[]" class="form-control sgst_rate" value="${(item.gst || 0)/2}" /></td>
            <td><input type="number" step="0.01" name="cgst[]" class="form-control cgst_rate" value="${(item.gst || 0)/2}" /></td>
            <td><input type="number" step="0.01" name="received_quantity[]" class="form-control received_quantity" value="${pending_qty}" /></td>
            <td class="pending_qty">${pending_qty.toFixed(2)}</td>
            <td><input type="number" step="0.01" name="price[]" class="form-control price" value="${item.price || 0}" /></td>
            <td class="amount">₹0.00</td>
            <td><button type="button" class="btn btn-danger btn-xs remove_row">Remove</button></td>
        `);
        $('#dynamic_field tbody').append(row);
        // Bind events
        row.find('.quantity, .received_quantity, .price, .sgst_rate, .cgst_rate, .gst_rate').change(function() {
            updatePendingQty(row);
            calculateRow(row);
            calculateTotals();
        });
        updatePendingQty(row);
        calculateRow(row); // Initial calc
    }

    function updatePendingQty(row) {
        var qty = parseFloat(row.find('.quantity').val()) || 0;
        var rec = parseFloat(row.find('.received_quantity').val()) || 0;
        row.find('.pending_qty').text(Math.max(0, qty - rec).toFixed(2));
    }
    
    // Add new row button if needed
    $('#add_row').click(function() {
        var row = $('<tr>');
        row.html(`
            <td><input type="text" name="term[]" class="form-control item_name" /></td>
            <td><input type="text" name="description[]" class="form-control description" /></td>
            <td><input type="number" name="quantity[]" class="form-control quantity" /></td>
            <td><input type="text" name="hsn[]" class="form-control hsn" /></td>
            <td><input type="number" step="0.01" name="gst_per[]" class="form-control gst_rate" /></td>
            <td><input type="number" step="0.01" name="sgst[]" class="form-control sgst_rate" /></td>
            <td><input type="number" step="0.01" name="cgst[]" class="form-control cgst_rate" /></td>
            <td><input type="number" step="0.01" name="received_quantity[]" class="form-control received_quantity" /></td>
            <td>-</td>
            <td><input type="number" step="0.01" name="price[]" class="form-control price" /></td>
            <td class="amount">₹0.00</td>
            <td><button type="button" class="btn btn-danger btn-xs remove_row">Remove</button></td>
        `);
        $('#dynamic_field tbody').append(row);
        row.find('.received_quantity, .price, .sgst_rate, .cgst_rate').change(function() {
            calculateRow(row);
            calculateTotals();
        });
    });
    
    // Remove row
    $(document).on('click', '.remove_row', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });
    
    // Form submit validation
    $('#add_name').submit(function(e) {
        var trs = $('#dynamic_field tbody tr');
        console.log('GRN Submit: Total rows found =', trs.length);
        if (trs.length === 0) {
            alert('Please add at least one item');
            e.preventDefault();
            return false;
        }
        var hasValidRow = false;
        trs.each(function(idx) {
            var $row = $(this);
            var $recInput = $row.find('.received_quantity');
            var $priceInput = $row.find('.price');
            var received = parseFloat($recInput.val()) || 0;
            var price = parseFloat($priceInput.val()) || 0;
            console.log('Row ' + idx + ':', {
                html: $row.html(),
                recInputFound: $recInput.length > 0,
                recValue: $recInput.val(),
                priceInputFound: $priceInput.length > 0,
                priceValue: $priceInput.val(),
                received: received,
                price: price
            });
            if (received > 0 && price > 0) {
                hasValidRow = true;
            }
        });
        console.log('GRN Submit: hasValidRow =', hasValidRow);
        if (!hasValidRow) {
            alert('Please fill received quantity and price for at least one valid row');
            e.preventDefault();
            return false;
        }
    });
    
    // Initial call
    updateAmountInWords();
});

