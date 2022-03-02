$(function(){

    // call the getCategories function()
    getCategories().then(data => populateCategories(data.categories)).catch(error => console.log(error));


    // call the getTags() fucntion
    getTags().then(data => populateTags(data.tags)).catch(err => console.log(err));
    
    // initialize summernote WYSIWYG editor
    $('#summernote').summernote({
        placeholder: 'write a snippet',
        height: 300
    });

    // initialize select2 plugin to render multiselect dropdown categories list instead the browser default select
    // Here I didn`t use select2 library validation or fetched dataSource like the select docs said.
    // I just used its styles to create a dropdown list contains the options to enhance UX.
    $('#cats').select2({ 
        placeholder: ' Search a category',
        closeOnSelect: false,
    });

    // initialize select2 plugin to render multiselect dropdown tags list instead the browser default select
    // Here I didn`t use select2 library validation or fetched dataSource like the select docs said.
    // I just used its styles to create a dropdown list contains the options to enhance UX.

    $('#tags').select2({
        placeholder: ' Search a tag',
        closeOnSelect: false,
    });


    // jQuery code to display the image file name when user selects an image file
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });

    // validate new tag added by the user
    $(document).on('click', '#addTag', function(){
        var newTagElement = $('#newTag');
        var newTagValue = newTagElement.val();
        if(validNewTag(newTagElement, existingTags($('#tags option'))))
        {
            sendNewTagToTheServer(newTagValue);
        }
    });


    // Validate form data
    $(document).on('click','#submit', function(e){
        e.preventDefault();
        var titleIsValid, summernoteIsValid, postImageIsValid, tagsAreValid, catsAreValid;
        if(validTitle()) titleIsValid = true;
        if(validSummernote()) summernoteIsValid = true;
        if(validPostImage()) postImageIsValid = true;
        if(validCats()) catsAreValid = true;
        if(validTags()) tagsAreValid = true;
        if(titleIsValid && summernoteIsValid && postImageIsValid && catsAreValid && tagsAreValid)
        {
            var formData = new FormData($('#postForm')[0]);
            sendToServer(formData); 
        }
        
    });


    /*************************************************/
    /*************************************************/
    /*************************************************/  
    /*  Write my fucntions used in my publish balde  */ 
    /*************************************************/
    /*************************************************/    
    /************************************************/

    // A function to validate postImage 
    function validPostImage() {
        let postImageInput = $('.custom-file-input');
        const postImageErrorContainer = $('#postImage-error');
        if(postImageInput.val() == '')
        {
            setError(postImageInput, postImageErrorContainer, '<strong>Please choose an image</strong>');
            return false;
        }
        else 
        {
            var extension = postImageInput.val().split('.').pop();
            var extPattern = /^(png|PNG|jpg|JPG|jpeg|JPEG|svg|SVG|bmp|BMP)$/;
            var imageSizeInBytes = (postImageInput)[0].files[0].size;
            if(!extension.match(extPattern))
            {
                setError(postImageInput, postImageErrorContainer, '<strong>image format have to be jpg,png,jpeg,svg</strong>');
                return false;
            }
            else if(imageSizeInBytes > 3500000)
            {
                setError(postImageInput, postImageErrorContainer, '<strong>image size have not to be greater than 3.5MB</strong>');
                return false;
            }
            else 
            {
                setSuccess(postImageInput, postImageErrorContainer);
                return true;
            }
        }
    }


    // A fucntion to get all tags form the database
    async function getTags(){
        const response = await fetch('/fetch-all-tags');
        const data = await response.json();
        return data; 
    }

    // A fucntion to get all categories from the database
    async function getCategories(){
        const response = await fetch('/fetch-all-categories');
        const data = await response.json();
        return data;
    }

    // A function to populate categories to the categories select box
    function populateCategories(categories) {
        $.each(categories, function (index, category) {
            $('#cats').append('<option value="' + category.id +'">' + category.cat_name + '</option>');
        });
    }

    // A function to populate tags to the tags select box
    function populateTags(tags) {
        $.each(tags, function (index, tag) {
            $('#tags').append('<option value="' + tag.id +'">' + tag.tag_name + '</option>');
        });
    }

    //  A function to set  error state and message
    function setError(element, errorContainer, errorMessage) {
        element.removeClass('is-valid');
        element.addClass('is-invalid');
        errorContainer.html(errorMessage);
        errorContainer.addClass('text-danger');
        errorContainer.removeClass('text-success');
    }

    //  A function to set success state and message
    function setSuccess(element, successContainer) {
        element.removeClass('is-invalid');
        successContainer.removeClass('text-danger');
        successContainer.html('');
    }

    // A function to validate post title
    function validTitle() {
        let title = $('#title');
        const pattern  = /^[\w\W-]{5,100}$/;
        const titleErrorContainer = $('#title-error');
        if(!pattern.test(title.val()))
        {
            setError(title, titleErrorContainer, '<strong>Please, enter a valid post title<br>\
            lowercase and uppercase characters<br>\
            symbols like . $ - % # ?<br>\
            Enter at least 5 characters and at most 100 characters</strong>');
            return false;
        }
        else if(pattern.test(title.val()))
        {
            setSuccess(title, titleErrorContainer);
            return true;
        }
    }

    //  A function to validate summernote Content depending summernote DOCS API
    function validSummernote() {
        const editor = $('.note-editor');
        const editorErrorContainer = $('#editor-error');
        const isEmptyEditor = $('#summernote').summernote('isEmpty');
        if (isEmptyEditor) {
            setError(editor, editorErrorContainer, '<strong>Post Can`t be blank!</strong>');
            return false;
        }
        else if(!isEmptyEditor)
        {
            setSuccess(editor, editorErrorContainer);
            return true;
        } 
    }

    // A fucntion to validate multiple select element
    function validCats()
    {
        var count = $("#cats :selected").length;
        if(count < 1 || count > 3)
        {
            setErrorsForSelect($('#cats-error'), '<strong>Categories should be 1 at least 3 at most</strong>');
            return false;
        }
        else{
            setSuccessForSelect($('#cats-error'));
            return true;
        }
    }

    // A fucntion To validate tags
    function validTags()
    {
        var count = $("#tags :selected").length;
        if(count < 3 || count > 10)
        {
            setErrorsForSelect($('#tags-error'), '<strong>Tags should be 3 at least 10 at most</strong>');
            return false;
        }
        else{
            setSuccessForSelect($('#tags-error'));
            return true;
        }
    }

    //  A fucntion to set errors to select element
    function setErrorsForSelect(errorContainer, errorMessage) {
        errorContainer.html(errorMessage);
        errorContainer.addClass('text-danger');
        errorContainer.removeClass('text-success');
    }

     //  A fucntion to set success to select element
     function setSuccessForSelect(successContainer) {
        successContainer.html('');
        successContainer.removeClass('text-danger');
    }

    // A fucntion to post data to server-side
    function sendToServer(data) {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            url: '/store',
            dataType: 'json',
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            error: function(xhr) {
                if(xhr.status === 200)
                {
                    location = '/home';
                }
                else if(xhr.status !== 200)
                {
                    errs = xhr.responseJSON.errors;
                    displayErrors(errs); 
                }
            }
        });
    }

    // A fucntion to display errors returned by the server
    function displayErrors(errors) {
        $('#errors').html('');
        $('#errors').addClass('alert alert-danger')
        $.each(errors, function(key, value){
            $('#errors').append('<p>' + value[0] + '</p>');
        });
    }

    // A fucntion to clear errors container 

    function clearErrors() {
        $('#errors').html('');
        $('#errors').removeClass('alert alert-danger');
    }

    // A fucntion to validate new tag added by the user
    function validNewTag(newTagElement, tagsArr) {
        const pattern  = /^[a-z.0-9-]{1,20}$/;
        const newTagErrorContainer = $('#new-tag-error');
        if(!pattern.test(newTagElement.val()))
        {
            setError(newTagElement, newTagErrorContainer, '<strong>Please, enter a valid tag<br>\
            Tag should only contain lowercase letters, digits and dot character<br>\
            Enter at least 1 character and at most 15 characters</strong>');
            return false;
        }
        else if($.inArray(newTagElement.val(), tagsArr) !== -1)
        {
            setError(newTagElement, newTagErrorContainer, '<strong>This tag is already existing in the tags select box</strong>');
            return false;
        }
        else if(pattern.test(newTagElement.val()))
        {
            setSuccess(newTagElement, newTagErrorContainer);
            return true;
        }
    }

    // A fucntion to send the newTag to the server
    function sendNewTagToTheServer(newTag) {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            url: '/store-tag',
            dataType: 'json',
            method: 'post',
            data: {tag: newTag},
            success: function(tag){
                clearErrors();
                $('#newTag').val('');
                // Prepend the new tag to the existing tags select box
                $('#tags').prepend('<option value="' + tag.id +'">' + tag.tag_name + '</option>');
                $('#new-tag-error').removeClass('text-success').text('');
                $('#newTag').removeClass('is-valid');
                $('.alert-container').addClass('alert alert-success')
                .text('Tag successfully added to the tags select box can choose it');
                setTimeout(function(){$('.alert-container').removeClass('alert alert-success').text('');}, 3000);
            },
            error: function(xhr){
                if(xhr.status !== 200)
                {
                    errs = xhr.responseJSON.errors;
                    displayErrors(errs); 
                }
            }
        });
    }

    // A function to get all tags from the select box
    function existingTags(selector) {
        var existingTags = [];
        $(selector).each(function()
        {
            // Add tags to my exsistingTags array
            existingTags.push($(this).text());
        });
        return existingTags;
    }


});