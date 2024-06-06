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

    const params = getUrlParams()

    const btn_file_dels = document.querySelectorAll(".btn_file_del")
    btn_file_dels.forEach((box) => {
        box.addEventListener("click" ,() => {

            if(!confirm('해당 첨부파일을 삭제하시갰습니까?')) {
                return false
            }

            const f = new FormData()
            f.append("th", box.dataset.th)
            f.append("bcode", params['bcode'])  //게시물 코드
            f.append("idx", params['idx'])      //게시물 번호
            f.append("mode", "each_file_del")   //모드 : 개별파일 삭제

            const xhr = new XMLHttpRequest()
            xhr.open("post", "pg/board_process.php", true)
            xhr.send(f)

            xhr.onload = () => {
                if(xhr.status == 200) {
                    
                    const data = JSON.parse(xhr.responseText)
                    if(data.result == 'empty_idx') {
                        alert('게시물 번호가 빠졌습니다.')
                    }
                    else if(data.result == 'empty_th') {
                        alert('몇 번째 첨부파일인지 알 수 없습니다.')
                    }
                    else if(data.result == 'success') {
                        self.location.reload()
                    }


                } else if(xhr.status == 404) {
                    alert('파일이 없습니다.')
                }
            }
        })
    })

})