/* Cargar data compañia */
loadCompanyData = async () => {
  const response = await fetch('/api/company');
  let data = await response.json();

  $('#company').html(data[0].company);
  $('#nit').html(data[0].nit);
  $('#city').html(data[0].city);
  $('#country').html(data[0].country);
  $('#phone').html(data[0].telephone);
  $('#address').html(data[0].address);

  data[0].logo
    ? (logo = `<img id="logo" src="${data[0].logo}" width="200" alt="">`)
    : (logo = '');
  $('#logo').html(logo);

  $('#qFooter').html(
    `Autorizo a ${data[0].company}. para recaudar, almacenar, utilizar y actualizar mis datos personales con fines exclusivamente comerciales y garantizándome que esta información no será revelada a terceros salvo orden de autoridad competente. Ley 1581 de 2012, Decreto 1377 de 2013.`
  );
};

$(document).ready(function () {
  loadCompanyData();
});
