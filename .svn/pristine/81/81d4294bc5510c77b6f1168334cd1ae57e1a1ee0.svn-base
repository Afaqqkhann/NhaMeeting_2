/*
Author: Tristan Denyer (based on Charlie Griefer's original clone code, and some great help from Dan - see his comments in blog post)
Plugin and demo at http://tristandenyer.com/using-jquery-to-duplicate-a-section-of-a-form-maintaining-accessibility/
Ver: 0.9.4
Date: Aug 25, 2013
*/
$(function () {
	$('#btnAdd').click(function () {
        var num     = $('.clonedInput').length, // Checks to see how many "duplicatable" input fields we currently have
            newNum  = new Number(num + 1),      // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via 
        newElem.find('.input_ln').attr('id', 'ID' + newNum + '_last_name').attr('name', 'ID' + newNum + '_last_name').val('');
  		newElem.find('.zne').val('');
 		 newElem.find('.pst').val('');
   newElem.find('.no_of_post').val('');
  newElem.find('.heading-reference').text('Post # ' + newNum);
  
  newElem.find('.exam_label').attr('for',  'required_exam_' + newNum).val('');
  newElem.find('.desc').html('');
  newElem.find('.total_marks').val('');
  newElem.find('.obtained_marks').val('');
  		
  
        // Color - checkbox
        newElem.find('.input_checkboxitem').attr('id', 'ID' + newNum + '_checkboxitem').attr('name', 'ID' + newNum + '_checkboxitem').val([]);
		newElem.find('.descr').html('');

        // Skate - radio
        newElem.find('.input_radio').attr('id', 'ID' + newNum + '_radioitem').attr('name', 'ID' + newNum + '_radioitem').val([]);

    // Insert the new element after the last "duplicatable" input field
        $('#entry' + num).after(newElem);
        $('#ID' + newNum + '_title').focus();

    // Enable the "remove" button. This only shows once you have a duplicated section.
        $('#btnDel').attr('disabled', false);

    // Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
        if (newNum == 5)
        $('#btnAdd').attr('disabled', true).prop('value', "Reached limit"); // value here updates the text in the 'add' button when the limit is reached 
    });

    $('#btnDel').click(function () {
    // Confirmation dialog box. Works on all desktop browsers and iPhone.
        if (confirm("Are you sure you wish to remove this section? This cannot be undone."))
            {
                var num = $('.clonedInput').length;
                // how many "duplicatable" input fields we currently have
                $('#entry' + num).slideUp('slow', function () {$(this).remove();
                // if only one element remains, disable the "remove" button
                    if (num -1 === 1)
                $('#btnDel').attr('disabled', true);
                // enable the "add" button
                $('#btnAdd').attr('disabled', false).prop('value', "Add More");});
            }
        return false; // Removes the last section you added
    // Enable the "add" button
    $('#btnAdd').attr('disabled', false);
    });
    // Disable the "remove" button
    $('#btnDel').attr('disabled', true);
});

/*
Author: Tristan Denyer (based on Charlie Griefer's original clone code, and some great help from Dan - see his comments in blog post)
Plugin and demo at http://tristandenyer.com/using-$-to-duplicate-a-section-of-a-form-maintaining-accessibility/
Ver: 0.9.4
Date: Aug 25, 2013
*/
$(function () {
	$('#btnAd').click(function () {
        var num     = $('.clonedInpu').length, // Checks to see how many "duplicatable" input fields we currently have
            newNum  = new Number(num + 1),      // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $('#entr' + num).clone().attr('id', 'entr' + newNum).fadeIn('slow'); // create the new element via 
        newElem.find('.editzone').val('');
      
	   newElem.find('.editpost').val('');

      newElem.find('.worker_qualification').val('');

      newElem.find('.worker_name').val('');

      newElem.find('.no_worker_under').val('');

      
        // Color - checkbox
        newElem.find('.input_checkboxitem').attr('id', 'ID' + newNum + '_checkboxitem').attr('name', 'ID' + newNum + '_checkboxitem').val([]);

        // Skate - radio
        newElem.find('.input_radio').attr('id', 'ID' + newNum + '_radioitem').attr('name', 'ID' + newNum + '_radioitem').val([]);

    // Insert the new element after the last "duplicatable" input field
        $('#entr' + num).after(newElem);
        $('#ID' + newNum + '_title').focus();

    // Enable the "remove" button. This only shows once you have a duplicated section.
        $('#btnDe').attr('disabled', false);

    // Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
        if (newNum == 5)
        $('#btnAd').attr('disabled', true).prop('value', "Reached limit"); // value here updates the text in the 'add' button when the limit is reached 
    });

    $('#btnDe').click(function () {
    // Confirmation dialog box. Works on all desktop browsers and iPhone.
        if (confirm("Are you sure you wish to remove this section? This cannot be undone."))
            {
                var num = $('.clonedInpu').length;
                // how many "duplicatable" input fields we currently have
                $('#entr' + num).slideUp('slow', function () {$(this).remove();
                // if only one element remains, disable the "remove" button
                    if (num -1 === 1)
                $('#btnDe').attr('disabled', true);
                // enable the "add" button
                $('#btnAd').attr('disabled', false).prop('value', "Add More");});
            }
        return false; // Removes the last section you added
    // Enable the "add" button
    $('#btnAd').attr('disabled', false);
    });
    // Disable the "remove" button
    $('#btnDe').attr('disabled', true);
});

//// third clone for multiple on one page
$(function () {
	$('#btnAd2d').click(function () {

        var num     = $('.clonedInpu2t').length, // Checks to see how many "duplicatable" input fields we currently have
            newNum  = new Number(num + 1),      // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $('#entr2y' + num).clone().attr('id', 'entr2y' + newNum).fadeIn('slow'); // create the new element via 
  		
  
        // Color - checkbox
        newElem.find('.input_checkboxitem').attr('id', 'ID' + newNum + '_checkboxitem').attr('name', 'ID' + newNum + '_checkboxitem').val([]);

        // Skate - radio
        newElem.find('.input_radio').attr('id', 'ID' + newNum + '_radioitem').attr('name', 'ID' + newNum + '_radioitem').val([]);

    // Insert the new element after the last "duplicatable" input field
        $('#entr2y' + num).after(newElem);
        $('#ID' + newNum + '_title').focus();

    // Enable the "remove" button. This only shows once you have a duplicated section.
        $('#btnDe2l').attr('disabled', false);

    // Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
        if (newNum == 3)
        $('#btnAd2d').attr('disabled', true).prop('value', "Reached limit"); // value here updates the text in the 'add' button when the limit is reached 
    });

    $('#btnDe2l').click(function () {
    // Confirmation dialog box. Works on all desktop browsers and iPhone.
        if (confirm("Are you sure you wish to remove this section? This cannot be undone."))
            {
                var num = $('.clonedInpu2t').length;
                // how many "duplicatable" input fields we currently have
                $('#entr2y' + num).slideUp('slow', function () {$(this).remove();
                // if only one element remains, disable the "remove" button
                    if (num -1 === 1)
                $('#btnDe2l').attr('disabled', true);
                // enable the "add" button
                $('#btnAd2d').attr('disabled', false).prop('value', "Add More");});
            }
        return false; // Removes the last section you added
    // Enable the "add" button
    $('#btnAd2d').attr('disabled', false);
    });
    // Disable the "remove" button
    $('#btnDe2l').attr('disabled', true);
});
