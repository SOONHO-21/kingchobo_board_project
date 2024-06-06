function getUrlParams() {
    const params = {};

    window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,
        function(str, key, value) {
            params[key] = value;
        }
    );

    return params;
}

function getExtensionOfFileName(filename) {
    const filelen = filename.length;    //문자열 길이
    const lastdot = filename.lastIndexOf('.');
    return filename.substring(lastdot + 1, filelen).toLowerCase();
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

    const id_attach = document.querySelector("#id_attach")
    if(id_attach) {
        id_attach.addEventListener("change", () => {
            const f = new FormData()

            f.append("bcode", params['bcode'])   //게시물 코드
            f.append("mode", "file_attach")   //모드 : 글 등록
            f.append("idx", params['idx'])   //모드 : 글 등록

            if(id_attach.files[0].size > 40 * 1024 * 1024){
                alert('파일 용량이 40메가보다 큰 파일이 첨부되었습니다.')
                id_attach.value = ''
                return false
            }

            ext = getExtensionOfFileName(id_attach.files[0].name);
            if(ext == 'txt' || ext == 'exe' || ext == 'xls' || ext == 'php' || ext == 'js') {
                alert('첨부할 수 없는 포멧의 파일이 첨부되었습니다.(exe, txt, php, js ..)')
                id_attach.value = ''
                return false
            }

            f.append("files", id_attach.files[0]) // 파일

            const xhr = new XMLHttpRequest()
            xhr.open("post", "./pg/board_process.php", true)
            xhr.send(f)

            xhr.onload = () => {
                if(xhr.status = 200) {
                    alert('통신 성공')
                } else if(xhr.status == 404) {
                    alert('통신 성공')
                }
            }

        })
    }

})