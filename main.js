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
            console.log("Файлы найдены");
            console.log(formData);
            
            // $.each(photoInput.files, (i, file) => {
            //     console.log(i);
            //     console.log(file);
            // })

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

                    // downloadButton.attr("hidden", true)

                },

                success: (data) => {
                    console.log("Запрос отработал");
                    console.log(data);
                    $(".submitButton").attr('disabled', false)
                    // downloadButton.removeAttr("hidden")
                    
                    // $("#download")[0].click() // Отвечает за мгновенное скачивание финальной фотки
                }, 

            })
        } else {
            // НЕ ВЫБРАНЫ ФАЙЛЫ И НАЖАТА КНОПКА
            console.log("Не выбраны файлы. Запрос не отправлен");
        }

    })

    $(".deleteFiles").click(() => {
        formData = new FormData()
        formData.append("size", 3)
        formData.append("orientation", 1)

        $.ajax({
            type: "POST",
            url: "test.php",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: () => {
                console.log("Запрос отправлен");
                $(".submitButton").attr('disabled')

            },

            success: (data) => {
                console.log("Запрос отработал");
                console.log(data);
                $(".submitButton").attr('disabled', false)
                // data - тут хранится всё что мы передали из php
            },

        })
    })
}

)



// console.log(Math.round(7 / 2));