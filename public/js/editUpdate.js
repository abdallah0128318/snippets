$(function(){

    // initialize summernote editor
    $('#summernote').summernote({
        height:300,
        // Sending ajax request to delete image file from the server after removing it from the editor using bin icon
        callbacks: {
            onMediaDelete : function(target) {
                sendToDelete($(target).attr('src'));
            },
        }
    });


    $()

    // initialize select2 plugin to render multiselect dropdown categories list instead the browser default select
    // Here I used autocomplete functionality that is provided by select2 jquery library with paginated results
    $('#cats').select2({
        placeholder: ' Search a category',
        closeOnSelect: false,
        maximumSelectionLength:3,
        minimumInputLength:1,
        ajax: {
            url: '/autocomplete-categories',
            dataType: 'json',
            Cache: true,
            delay: 250,
            data: function(params){
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            }
        }
    });

    // initialize select2 plugin to render multiselect dropdown Tags list instead the browser default select
    // Here I used autocomplete functionality that is provided by select2 jquery library with paginated results
    $('#tags').select2({
        placeholder: ' Search a tag',
        closeOnSelect: false,
        maximumSelectionLength:10,
        minimumInputLength:1,
        ajax: {
            url: '/autocomplete-tags',
            dataType: 'json',
            Cache: true,
            delay: 250,
            data: function(params){
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            }
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


    // Validate edit form data then send to the server using Ajax if it is valid
    // Here i applied a fron-end validation then i will also apply a server-side validation
    $(document).on('click','#update', function(e){
        e.preventDefault();
        var titleIsValid, summernoteIsValid, postImageIsValid, catsAreValid, tagsAreValid;
        if(validTitle()) titleIsValid = true;
        if(validSummernote()) summernoteIsValid = true;
        if(validPostImage()) postImageIsValid = true;
        if(minimumIsValid(1, $('#cats'), $('#cats-error'), '<strong>You have to select 1 category at least!</strong>')) 
        catsAreValid = true;
        if(minimumIsValid(3, $('#tags'), $('#tags-error'), '<strong>You have to select 3 tag at least!</strong>'))
        tagsAreValid = true;

        // send form data to the server if it is valid
        if(titleIsValid && summernoteIsValid && postImageIsValid && catsAreValid && tagsAreValid)
        {
            var formData = new FormData($('#editForm')[0]);
            sendFormToServer(formData); 
        }
    });




    /*************************************************/
    /*************************************************/
    /*************************************************/  
    /*    Write my fucntions used in my edit balde   */ 
    /*************************************************/
    /*************************************************/    
    /************************************************/



    // a function to send summernote image src to the server to be deleted when a user remove it from the editor
    function sendToDelete(imageSrc) {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            method: 'POST',
            url: '/deleteImage',
            data: {src: imageSrc}
        });
    }



    // A fucntion to validate the title
    function validTitle() {
        let title = $('#title');
        const pattern  = /^[\w\W-]{5,100}$/;
        const titleErrorContainer = $('#title-error');
        if(!pattern.test(title.val()))
        {
            setError(titleErrorContainer, '<strong>Please, enter a valid post title<br>\
            lowercase and uppercase characters<br>\
            symbols like . $ - % # ?<br>\
            Enter at least 5 characters and at most 100 characters</strong>');
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
            setError(editorErrorContainer, '<strong>Post Can`t be blank!</strong>');
            return false;
        }
        else if(!isEmptyEditor)
        {
            setSuccess(editorErrorContainer);
            return true;
        } 
    }

    // A function to validate postImage 
    function validPostImage() {
        let postImageInput = $('.custom-file-input');
        const postImageErrorContainer = $('#postImage-error');
        if(postImageInput.val() != '')
        {
            var extension = postImageInput.val().split('.').pop();
            var extPattern = /^(png|PNG|jpg|JPG|jpeg|JPEG|svg|SVG|bmp|BMP)$/;
            if(!extension.match(extPattern))
            {
                setError(postImageErrorContainer, '<strong>image format have to be jpg,png,jpeg,svg,bmp</strong>');
                return false;
            }
            else 
            {
                setSuccess(postImageErrorContainer);
                return true;
            }
        }
        return true;
    }

    // A function to check  the minimum number of items selected in multiple select box for tags and categories
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

    // A fucntion to set error state and error message
    function setError(errorContainer, errorMessage) {
        errorContainer.html(errorMessage);
        errorContainer.addClass('text-danger');
    }

    //  A function to set success state
    function setSuccess(successContainer) {
        successContainer.removeClass('text-danger');
        successContainer.html('');
    }

    // A fucntion to display errors returned by the server
    function displayErrors(errors) {
        $('#errors').html('');
        $('#errors').addClass('alert alert-danger')
        $.each(errors, function(key, value){
            $('#errors').append('<p>' + value[0] + '</p>');
        });
    }

    // A fucntion to send form data to the server using Ajax post request
    function sendFormToServer(data) {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            url: '/updatePost',
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

    // A fucntion to send the newTag to the server
    function sendNewTagToTheServer(newTag) {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            url: '/store-tag',
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
                    $('#new-tag-feedback').addClass('text-danger').html('<strong>' + errs.tag[0] + '</strong>');
                }
            }
        });
    }

});