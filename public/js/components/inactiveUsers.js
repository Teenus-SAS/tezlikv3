$(document).ready(function () {
  findInactiveUsers = async () => {
    try {
      result = await $.ajax({
        url: '/api/userSession/checkLastLoginUsers',
      });

      if (result.reload) {
        location.reload();
      }
      // console.log(result);
    } catch (error) {
      console.log(error);
    }
  };

  //findInactiveUsers();
});
