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

    params = getUrlParams();

    //글 목록 버튼 클릭
    const btn_list = document.querySelector("#btn_list")
    btn_list.addEventListener("click", () => {
        self.location.href='./board.php?bcode=' + params['bcode'];
    })

    //글 수정 버튼 클릭
    const btn_edit = document.querySelector("#btn_edit")
    if(btn_edit) {
        btn_edit.addEventListener("click", () => {
            self.location.href='./board_edit.php?bcode=' + params['bcode'] + '&idx=' + params['idx'];
        })
    }

    //글 삭제 버튼 클릭
    const btn_delete = document.querySelector("#btn_delete")
    if(btn_delete) {
        btn_delete.addEventListener("click", () => {
            if(confirm('삭제하시겠습니까?')){



                const f = new FormData()
                f.append('idx', params['idx'])
                f.append('bcode', params['bcode'])
                f.append('mode', 'delete')

                const xhr = new XMLHttpRequest()
                xhr.open('post', 'pg/board_process.php', true)
                xhr.send(f)

                xhr.onload = () => {
                    if(xhr.status == 200) {

                        const data = JSON.parse(xhr.responseText)
                        if(data.result == 'success') {
                            alert('게시물이 삭제되었습니다.')
                            self.location.href='./board.php?bcode=' + params['bcode']
                        }
                    } else if(xhr.status == 404) {
                        alert('통신실패')
                    }
                }
            }
        })
    }
    

})