$(document).ready(function () {
    actualDate = () => {
        const date = new Date();

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        const formatDate = `${year}-${month}-${day}`;
  
        return formatDate;
    }
});