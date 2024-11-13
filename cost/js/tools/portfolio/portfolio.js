$(document).ready(function () {
    $(".selectNavigation").click(function (e) {
        e.preventDefault();

        let option = this.id;

        const sections = {
            "link-ENegotiation": ".cardEfficientNegotiations",
            "link-portfolio": ".cardPortfolio",
        };

        // Ocultar todas las secciones
        $(
            ".cardEfficientNegotiations, .cardPortfolio"
        ).hide();

        // Mostrar la sección correspondiente según la opción seleccionada
        $(sections[option] || "").show();

        let tables = document.getElementsByClassName("dataTable");

        for (let table of tables) {
            table.style.width = "100%";
            table.style.width = "100%";
        }
    });
});