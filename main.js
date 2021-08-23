$(document).ready(() => {
    $(".submitButton").click(() => {

        formData = new FormData()

        photoInput = $("#photoInput")[0]


        // console.log(photoInput.files);

        if (photoInput.files.length != 0) {
            formData.append("photo", photoInput.files[0])
            // console.log(photoInput.files[0]);
            // $.each(photoInput.files, (i, file) => {
            //     console.log(i);
            //     console.log(file);
            // })
        } else {
            // НЕ ВЫБРАНЫ ФАЙЛЫ И НАЖАТА КНОПКА
        }

        
        console.log(formData);

        $.ajax({
            type: "POST",
            url: "form.php",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: (data) => {
                // console.log(data);
                // data - тут хранится всё что мы передали из php
            },

        })
    })
}

)

// console.log(Math.round(7 / 2));