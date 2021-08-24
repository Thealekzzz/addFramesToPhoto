function getFilename(str) {
    // Возвращает имя файла без .расширение

    return str.substr(0, str.lastIndexOf("."));
}


function getFileextention(str) {
    // Возвращает только расширение файла

    return str.substr(str.lastIndexOf("."));
}


function removeLastUploadedFiles(file) {
    removingData = new FormData()
    removingData.append("filename", getFilename(file))
    removingData.append("fileextension", getFileextention(file))

    $.ajax({
        type: "POST",
        url: "deleteFiles.php",
        data: removingData,
        cache: false,
        contentType: false,
        processData: false,

        beforeSend: () => {
            console.log("Запрос на удаление отправлен");

        },

        success: (data) => {
            console.log("Запрос на удаление отработан");
            // data - тут хранится всё что мы передали из php
        },

    })
}


const frameNames = ["brazilian barnwood.png", "classic black.png", "classic brown.png", "metallic silver.png", "modern black.png", "modern brown.png", "modern white.png"];


$(document).ready(() => {
    $(".submitButton").click(() => {

        photoInput = $("#photoInput")[0]
        downloadButton = $(".downloadButton")


        // console.log(photoInput.files);

        if (photoInput.files.length != 0) {
            formData = new FormData()
            formData.append("photo", photoInput.files[0])
            formData.append("size", 3)
            formData.append("orientation", 0)


            $.ajax({
                type: "POST",
                url: "form.php",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,

                beforeSend: () => {
                    console.log("Запрос отправлен");
                    $(".submitButton").attr('disabled', true)


                    // Удаление всех последних сделанных файлов
                    if (localStorage.getItem("lastUploadedPhoto") != null) {
                        removeLastUploadedFiles(localStorage.getItem("lastUploadedPhoto"))
                        localStorage.removeItem("lastUploadedPhoto")

                    }

                },

                success: (data) => {
                    console.log("Запрос отработан");
                    console.log(data);
                    setTimeout(() => {
                        $(".submitButton").attr('disabled', false)
                        
                    }, 1000);

                    $(".imageContainer")
                    .css('background', "url(images/results/" + getFilename(photoInput.files[0].name) + "/" + getFilename(photoInput.files[0].name) + "_FINAL.jpg) left no-repeat")
                    .css('background-size', 'contain')
                    
                    $("#download").attr("href", "images/results/" + getFilename(photoInput.files[0].name) + "/" + getFilename(photoInput.files[0].name) + "_FINAL.jpg")
                    $("#download")[0].click() // Отвечает за мгновенное скачивание финальной фотки

                    // Добавляем название загруженного фото в localStorage
                    localStorage.setItem("lastUploadedPhoto", photoInput.files[0].name)
                }, 

            })
        } else {
            // НЕ ВЫБРАНЫ ФАЙЛЫ И НАЖАТА КНОПКА
            console.log("Не выбраны файлы. Запрос не отправлен");
        }

    })


    $(window).on("beforeunload", function() {
        // Удаление всех последних сделанных файлов
        if (localStorage.getItem("lastUploadedPhoto") != null) {
            removeLastUploadedFiles(localStorage.getItem("lastUploadedPhoto"))
            localStorage.removeItem("lastUploadedPhoto")

        }

	});

})





// console.log(Math.round(7 / 2));