$(document).ready(function () {
  findInactiveUsers = async () => {
    try {
      result = await $.ajax({
        url: '/api/checkLastLoginUsers',
      });

      if (result.reload) {
        location.reload();
      }
      // console.log(result);
    } catch (error) {
      console.log(error);
    }
  };

  findInactiveUsers();
});
