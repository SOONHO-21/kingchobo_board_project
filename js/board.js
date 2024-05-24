function getUrlParams() {
    const params = {};

    window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,
        function(str, key, value) {
            params[key] = value;
        }
    );

    return params;
}

document.addEventListener("DOMContentLoaded", () => {
    const btn_write = document.querySelector("#btn_write")
    btn_write.addEventListener("click", () => {
        
        const params = getUrlParams()

        self.location.href='./board_write.php?bcode=' + params['bcode'];
    })
})