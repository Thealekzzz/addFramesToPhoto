$(document).ready(() => {
    $(".submitButton").click(() => {

        photoInput = $("#photoInput")[0]
        downloadButton = $(".downloadButton")


        // console.log(photoInput.files);

        if (photoInput.files.length != 0) {
            formData = new FormData()
            formData.append("photo", photoInput.files[0])
            console.log("Файлы найдены");
            
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
                    downloadButton.attr("hidden", true)

                },

                success: (data) => {
                    console.log("Запрос отработал");
                    console.log(data);
                    // data - тут хранится всё что мы передали из php
                    // downloadButton.removeAttr("hidden")
                    $("#download")[0].click()
                    console.log('$("#download"): ', $("#download"));
                },

            })
        } else {
            // НЕ ВЫБРАНЫ ФАЙЛЫ И НАЖАТА КНОПКА
            console.log("Не выбраны файлы. Запрос не отправлен");
        }

    })

    $(".deleteFiles").click(() => {
        $.ajax({
            type: "POST",
            url: "test.php",
            data: "Ало",
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: () => {
                console.log("Запрос отправлен");

            },

            success: (data) => {
                console.log("Запрос отработал");
                console.log(data);
                // data - тут хранится всё что мы передали из php
            },

        })
    })
}

)



// console.log(Math.round(7 / 2));