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

let sizeNum = "1"
let orientationNum = "0";

const pickButton = $(".pickPhoto")
const sizeButtons = $("[data-size]")
const orientationButtons = $("[data-orientation]")

const imageContainer = document.querySelector(".imageContainer")

sizeButtons.each(i => {
    sizeButtons[i].addEventListener("click", () => {
        sizeButtons[sizeNum].removeAttribute("active")
        sizeButtons[i].setAttribute("active", true)
        sizeNum = sizeButtons[i].getAttribute("data-size");

    })
})

orientationButtons.each(i => {
    orientationButtons[i].addEventListener("click", () => {
        orientationButtons[orientationNum].removeAttribute("active")
        orientationButtons[i].setAttribute("active", true)
        orientationNum = orientationButtons[i].getAttribute("data-orientation");

    })
})

$(".pickPhoto").click(() => {
    $("#photoInput").click()
})

$("#photoInput").change(() => {
    $(".pickPhoto")[0].innerText = $("#photoInput")[0].files[0].name
    // console.log($("#photoInput")[0].files[0].name);

})


$(document).ready(() => {
    // Нажата кнопка сгенерировать
    $(".submitButton").click(() => {

        photoInput = $("#photoInput")[0]
        downloadButton = $(".downloadButton")


        // console.log(photoInput.files);

        if (photoInput.files.length != 0) {
            formData = new FormData()
            formData.append("photo", photoInput.files[0])
            formData.append("size", sizeNum)
            formData.append("orientation", orientationNum)


            $.ajax({
                type: "POST",
                url: "createPhoto.php",
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
                    
                    $(".imageContainer").css('background', "")

                    $(".imageContainer")
                    .css('background', "url('images/results/" + getFilename(photoInput.files[0].name) + "/" + getFilename(photoInput.files[0].name) + "_FRAMED.jpg') center no-repeat")
                    .css('background-size', 'contain')

                    if (orientationNum == "1") {
                        // Выбрана горизонтальная картинка. Делаю вертикальное поле
                        imageContainer.classList.add("imageContainerVertical")
                    } else {
                        // Выбрана вертикальная картинка. Делаю горизонтальное поле
                        imageContainer.classList.remove("imageContainerVertical")
                    }
                    

                    $(".imageContainer span").attr("hidden", true)
                    
                    $("#download").attr("href", "images/results/" + getFilename(photoInput.files[0].name) + "/" + getFilename(photoInput.files[0].name) + "_FRAMED.jpg")
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


    // Обновление или закрытие страницы
    $(window).on("beforeunload", function() {
        // Удаление всех последних сделанных файлов
        if (localStorage.getItem("lastUploadedPhoto") != null) {
            removeLastUploadedFiles(localStorage.getItem("lastUploadedPhoto"))
            localStorage.removeItem("lastUploadedPhoto")

        }

	});


    // Нажатие кнопки dEBUG
    $(".DEBUG").click(() => {
        if (photoInput.files.length != 0) {
            formData = new FormData()
            formData.append("photo", photoInput.files[0])
            formData.append("size", sizeNum)
            formData.append("orientation", orientationNum)


            $.ajax({
                type: "POST",
                url: "createPhoto.php",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,

                beforeSend: () => {
                    console.log("Запрос DEBUG отправлен");
                    $(".DEBUG").attr('disabled', true)

                    if (localStorage.getItem("lastUploadedPhoto") != null) {
                        removeLastUploadedFiles(localStorage.getItem("lastUploadedPhoto"))
                        localStorage.removeItem("lastUploadedPhoto")

                    }

                },

                success: (data) => {
                    console.log("Запрос DEBUG отработан");
                    console.log(data);
                    setTimeout(() => {
                        $(".DEBUG").attr('disabled', false)
                        
                    }, 1000);


                    // Добавляем название загруженного фото в localStorage
                    localStorage.setItem("lastUploadedPhoto", photoInput.files[0].name)
                    
                }, 

            })
        }
    })
})





// console.log(Math.round(7 / 2));