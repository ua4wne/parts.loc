<script>
    $(".form-horizontal").submit(function (event) {
        if ($(this).data("function") === 'no_delete') {
            return true;
        }
        var x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
        if (x) {
            return true;
        }
        else {

            event.preventDefault();
            return false;
        }

    });
    $(".form-inline").submit(function (event) {
        var x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
        if (x) {
            return true;
        }
        else {

            event.preventDefault();
            return false;
        }

    });
</script>
