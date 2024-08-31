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

    // 글 목록 버튼 클릭
    const btn_list = document.querySelector("#btn_list")
    btn_list.addEventListener("click", () => {
        self.location.href='./board.php?bcode=' + params['bcode']
    })

    // 글 수정 버튼 클릭
    const btn_edit = document.querySelector("#btn_edit")
    if(btn_edit) {
        btn_edit.addEventListener("click", () => {
            self.location.href='./board_edit.php?bcode=' + params['bcode'] + '&idx=' + params['idx']
        })
    }

    // 글삭제 버튼 클릭
    const btn_delete = document.querySelector("#btn_delete")
    if(btn_delete) {
        btn_delete.addEventListener("click", () => {
        if(confirm('삭제하시겠습니까?')) {

            const f = new FormData()
            f.append('idx', params['idx'])
            f.append('bcode', params['bcode'])
            f.append('mode', 'delete')      //삭제 모드

            // XMLHttpRequest 객체를 생성하여 서버와 비동기 통신
            const xhr = new XMLHttpRequest()
            xhr.open('post', 'pg/board_process.php', true)
            xhr.send(f)

            xhr.onload = () => {
                if(xhr.status == 200) {     //통신 응답이 성공적이면
                    
                    const data = JSON.parse(xhr.responseText)   // JSON 형식의 응답을 JavaScript 객체로 변환
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

    // 댓글 등록 버튼 클릭시
    const btn_comment = document.querySelector("#btn_comment")
    btn_comment.addEventListener("click", () => {
        const comment_content = document.querySelector("#comment_content")      // comment_content : board_view.php의 댓글 <textarea>의 id
        if(comment_content.value == '') {   // <textarea> 내용이 빔
            alert('댓글 내용을 입력바랍니다.')
            comment_content.focus()
            return false
        }
        // comment_content.value
        // 작성자 아이디 : 세션
        // 글번호 : params['idx']
        const f1 = new FormData()
        f1.append('pidx', params['idx'])
        f1.append('content', comment_content.value)
        f1.append('idx', btn_comment.dataset.commentIdx)    //댓글 번호를 commentIdx라는 값으로 세팅

        if(btn_comment.dataset.commentIdx > 0) {
            f1.append('mode', 'edit')
        } else {
            f1.append('mode', 'input')
        }
        
        // XMLHttpRequest 객체를 생성하여 서버와 비동기 통신
        const xhr = new XMLHttpRequest()
        xhr.open("post", "./pg/comment_process.php", true)
        xhr.send(f1)
        xhr.onload = () => {
            if(xhr.status == 200) {
                const data = JSON.parse(xhr.responseText)
                if(data.result == 'empty pidx') {
                    alert('게시물 번호가 빠졌습니다.')
                    return false
                }else if(data.result == 'content') {
                    alert('댓글 내용이 빠졌습니다.')
                    comment_content.focus()
                    return false
                }else if(data.result == 'success') {
                    self.location.reload();
                }

            }else if(xhr.status == 404) {
                alert('통신실패')
            }
        }
    })

    // 댓글 삭제 버튼 클릭시
    const btn_comment_deletes = document.querySelectorAll(".btn_comment_delete")
    btn_comment_deletes.forEach((box) => {
        box.addEventListener("click", () => {

            if(!confirm('이 댓글을 삭제하시겠습니까?')) {
                return false
            }

            const f1 = new FormData()
            f1.append('pidx', params['idx'])    //게시글 번호
            f1.append('idx', box.dataset.commentIdx)    //댓글 번호를 commentIdx라는 값에서 가져옴
            f1.append('mode', 'delete')     //삭제 모드

            const xhr = new XMLHttpRequest()
            xhr.open("post", "pg/comment_process.php", true)    //삭제 프로세스 가동
            xhr.send(f1)
            xhr.onload = () => {
                if(xhr.status == 200) {
                    const data = JSON.parse(xhr.responseText)
                    if(data.result == 'success') {
                        self.location.reload()
                    }
                }else if(xhr.status == 404) {
                    alert('통신실패')
                }
            }
        })
    })

    // 댓글 수정 버튼 클릭시
    const btn_comment_edits = document.querySelectorAll(".btn_comment_edit")
    btn_comment_edits.forEach( (box) => {
        box.addEventListener("click", () => {
            const comment_content = document.querySelector("#comment_content")
            comment_content.value = box.parentNode.childNodes[0].textContent    //기존에 입력된 내용 표시
            comment_content.style.backgroundColor = "Khaki"
            comment_content.focus()     //커서 두기

            btn_comment.dataset.commentIdx = box.dataset.commentIdx     //버튼의 댓글 idx를 설정
            btn_comment.textContent = '수정'        //버튼의 문구
        })
    })
})