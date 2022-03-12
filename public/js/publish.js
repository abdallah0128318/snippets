$(function(){
    
    // initialize summernote WYSIWYG editor
    $('#summernote').summernote({
        placeholder: 'write a snippet',
        height: 300
    });

    // initialize select2 plugin to render multiselect dropdown categories list instead the browser default select
    // Here I used autocomplete functionality that is provided by select2 jquery library with paginated results
    $('#cats').select2({ 
        placeholder: ' Search a category',
        closeOnSelect: false,
        multiple: true,
        maximumSelectionLength:3,
        minimumInputLength:1,
        ajax: {
            data: function(params)
            {
                return {
                    term: params.term || '',
                    page: params.page || 1
                };
            },
            url: route('paginated.categories'),
            dataType: 'json',
            Cache: true
        }
    });

    // initialize select2 plugin to render multiselect dropdown Tags list instead the browser default select
    // Here I used autocomplete functionality that is provided by select2 jquery library with paginated results

    $('#tags').select2({
        placeholder: ' Search a tag',
        closeOnSelect: false,
        multiple: true,
        maximumSelectionLength:10,
        minimumInputLength:1,
        ajax: {
            data: function(params)
            {
                return {
                    term: params.term || '',
                    page: params.page || 1
                };
            },
            url: route('paginated.tags'),
            dataType: 'json',
            Cache: true
        }
    });


    // jQuery code to display the image file name when user selects an image file from his device
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });

    // added only server-side validation to the new tag
    $(document).on('click', '#addTag', function(){
        var newTagElement = $('#newTag');
        var newTagValue = newTagElement.val();
        sendNewTagToTheServer(newTagValue);
    });


    // Validate form data
    $(document).on('click','#Publish', function(e){
        e.preventDefault();
        var titleIsValid, summernoteIsValid, postImageIsValid, tagsAreValid, catsAreValid;
        if(validTitle()) titleIsValid = true;
        if(validSummernote()) summernoteIsValid = true;
        if(validPostImage()) postImageIsValid = true;
        if(minimumIsValid(1, $('#cats'), $('#cats-error'), '<i class="validation">You have to select 1 category at least!</i>')) catsAreValid = true;
        if(minimumIsValid(3, $('#tags'), $('#tags-error'), '<i class="validation">You have to select 3 tag at least!</i>')) tagsAreValid = true;
        if(titleIsValid && summernoteIsValid && postImageIsValid && catsAreValid && tagsAreValid)
        {
            var formData = new FormData($('#postForm')[0]);
            sendFormToServer(formData); 
        }
    });


    /*************************************************/
    /*************************************************/
    /*************************************************/  
    /*  Write my fucntions used in my publish balde  */ 
    /*************************************************/
    /*************************************************/    
    /************************************************/


    // A function to check  the minimum number of items selected in multiple select box

    function minimumIsValid(minimum, element, errorContainer, errorMessage)
    {
        if(element.select2('data').length < minimum)
        {
            setError(errorContainer, errorMessage);
            return false;
        }
        else {
            setSuccess(errorContainer);
            return true;
        }
            
    }
    

    // A function to validate postImage 
    function validPostImage() {
        let postImageInput = $('.custom-file-input');
        const postImageErrorContainer = $('#postImage-error');
        if(postImageInput.val() == '')
        {
            setError(postImageErrorContainer, '<i class="validation">Please choose an image!</i>');
            return false;
        }
        else 
        {
            var extension = postImageInput.val().split('.').pop();
            var extPattern = /^(png|PNG|jpg|JPG|jpeg|JPEG|svg|SVG|bmp|BMP)$/;
            if(!extension.match(extPattern))
            {
                setError(postImageErrorContainer, '<i class="validation">image format have to be jpg,png,jpeg,svg,bmp</i>');
                return false;
            }
            else 
            {
                setSuccess(postImageErrorContainer);
                return true;
            }
        }
    }


    //  A function to set  error state and message
    function setError(errorContainer, errorMessage) {
        errorContainer.html(errorMessage);
        errorContainer.addClass('text-danger');
    }

    //  A function to set success state
    function setSuccess(successContainer) {
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
            setError(titleErrorContainer, '<i class="validation">Title has to be 5 to 100 letter!</i>');
            return false;
        }
        else if(pattern.test(title.val()))
        {
            setSuccess(titleErrorContainer);
            return true;
        }
    }

    //  A function to validate summernote Content depending summernote DOCS API
    function validSummernote() {
        const editorErrorContainer = $('#editor-error');
        const isEmptyEditor = $('#summernote').summernote('isEmpty');
        if (isEmptyEditor) {
            setError(editorErrorContainer, '<i class="validation">Post Can`t be blank!</i>');
            return false;
        }
        else if(!isEmptyEditor)
        {
            setSuccess(editorErrorContainer);
            return true;
        } 
    }


    // A fucntion to post data to server-side
    function sendFormToServer(data) {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            url: route('store.post'),
            dataType: 'json',
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            error: function(xhr) {
                if(xhr.status === 200)
                {
                    location = route('home');
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

    
    // A fucntion to send the newTag to the server
    function sendNewTagToTheServer(newTag) {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            url: route('store.new.tag'),
            dataType: 'json',
            method: 'post',
            data: {tag: newTag},
            success: function(data){
                $('#newTag').val('');
                $('#new-tag-feedback').removeClass('text-danger').addClass('alert alert-success text-center').html(data.msg);
                setTimeout(function(){$('#new-tag-feedback').removeClass('alert alert-success').html('');}, 3000);
            },
            error: function(xhr){
                if(xhr.status !== 200)
                {
                    errs = xhr.responseJSON.errors;
                    $('#new-tag-feedback').addClass('text-danger').html('<i class="validation">' + errs.tag[0] + '</i>');
                }
            }
        });
    }


});