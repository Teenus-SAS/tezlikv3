$(document).ready(function() {

    searchData = async(urlApi) => {
        try {
            result = await $.ajax({ url: urlApi })
            return result
        } catch (error) {
            console.error(error)
        }
    }

    sendDataPOST = async(urlApi, params) => {
        try {
            result = await $.ajax({
                url: urlApi,
                type: 'POST',
                data: params
            })
            return result
        } catch (error) {
            console.error(error)
        }
    }

})