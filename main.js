// В этом файле намешан JS и JQuerry, но мне пофик, потому что надо было как можно быстрее склепать сайт


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


function removeAllImages() {
    $.ajax({
        type: "POST",
        url: "clearImages.php",
        cache: false,
        contentType: false,
        processData: false,

        beforeSend: () => {
            console.log("Запрос на удаление отправлен");

        },

        success: (data) => {
            console.log("Запрос на удаление отработан");
            console.log(data);
            // в data хранится всё что мы передали из php
        },
    })
}


function addAnimationClasses(obj, before="invisible", after="notDisplayed", condition="invisible") {
    if (obj.hasClass(condition)) {
        obj.removeClass(after)
        setTimeout(() => {
            obj.removeClass(before)
        }, 10);

    } else {
        obj.addClass(before)
        setTimeout(() => {
            obj.addClass(after)
        }, 300);

    }
}


function invalidPhoto(msg) {
    offsets = [-30, 20, -15, 10, -6, 4, -2, 1, 0]
    pickButton[0].style.borderColor = "red"
    pickButton[0].style.color = "red"
    iter = 0

    console.log(msg);

    temp = setInterval(() => {
        if (iter < offsets.length) {
            pickButton[0].style.transform = "translateX(" + offsets[iter] + "px)"
        } else {
            clearInterval(temp)
        }
        
        iter++

    }, 80);

    setTimeout(() => {
        pickButton[0].style.borderColor = "black"
        pickButton[0].style.color = "black"
    }, offsets.length * 80);
}


let TEMPSTR

const frameNames = ["brazilian barnwood.png", "classic black.png", "classic brown.png", "metallic silver.png", "modern black.png", "modern brown.png", "modern white.png"];

let sizeNum = "1"
let orientationNum = "0";
let downloadToggleState = true

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
    // Показ сообщения о загрузке файлов не более 8Мб
    $(".pickPhoto").mouseenter(() => {
        $(".attention").removeClass("notDisplayed")
        setTimeout(() => {
            $(".attention").removeClass("invisible")
        }, 10);
    })

    $(".pickPhoto").mouseleave(() => {
        $(".attention").addClass("invisible")
        setTimeout(() => {
            $(".attention").addClass("notDisplayed")
        }, 300);
    })

    // Выбор, скачивать ли фото по готовности
    $(".toggle").click(() => {
        downloadToggleState = !downloadToggleState
        $(".toggleSwitcher").toggleClass("toggleSwitcherOff")
    })

    // Нажатие кнопки скачивания
    $(".downloadButton").click(() => {
        $("#download")[0].click() // Клик по ссылке на скачивание финальной фотки

    })

    // Нажатие галочки на сообщении
    $(".sepInfo img").click(() => {
        $(".sepInfo").addClass("invisible")
        setTimeout(() => {
            $(".sepInfo").addClass("notDisplayed")
        }, 300);
        localStorage.setItem("sepInfoMessage", true)
    })

    // Нажатие кнопки отдельного скачивания
    $(".separateDownloadButton").click(() => {
        $("#sepDownload").attr("href", "images/results/" + getFilename(photoInput.files[0].name) + "/" + getFilename(photoInput.files[0].name) + "_" + frameNames[$("#choosePhoto").val()])
        $("#sepDownload")[0].click()

    })

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
            formData.append("OS", navigator.userAgent)


            $.ajax({
                type: "POST",
                url: "createPhoto.php",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,

                beforeSend: () => {
                    // console.log("Запрос отправлен");
                    $(".submitButton").attr('disabled', true)
                    $(".downloadButton").attr('disabled', true)
                    $(".separateDownloadButton").attr('disabled', true)


                    // Удаление всех последних сделанных файлов
                    if (localStorage.getItem("lastUploadedPhoto") != null) {
                        removeLastUploadedFiles(localStorage.getItem("lastUploadedPhoto"))
                        localStorage.removeItem("lastUploadedPhoto")

                    }

                },

                success: (data) => {
                    // console.log("Запрос отработан");
                    dataBack = data.split("{")
                    console.log(dataBack[1]);

                    if (dataBack.length <= 2) {
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
                        if (downloadToggleState) {
                            $("#download")[0].click() // Отвечает за мгновенное скачивание финальной фотки

                        }
                        $(".downloadButton").attr('disabled', false)
                        $(".separateDownloadButton").attr('disabled', false)

                        // Если сообщение о раздельном скачивании еще не было показано - показать
                        if (localStorage["sepInfoMessage"] == undefined) {
                            $(".sepInfo").removeClass("notDisplayed")
                            setTimeout(() => {
                                $(".sepInfo").removeClass("invisible")
                            }, 10);

                        }
                        
                    } else {
                        invalidPhoto("Фото неверного расширения")
                    }

                    // Добавляем название загруженного фото в localStorage
                    localStorage.setItem("lastUploadedPhoto", photoInput.files[0].name)
                }, 

            })
        } else {
            // НЕ ВЫБРАНЫ ФАЙЛЫ И НАЖАТА КНОПКА
            invalidPhoto("Не выбрано фото")
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



    })

})





// console.log(Math.round(7 / 2));