function getUrlParams() {     
    const params = {};  
    
    window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, 
      function(str, key, value) { 
          params[key] = value; 
        }
    );     
  
    return params; 
  }
  
  function getExtensionOfFilename(filename) {   // 확장자 추출
    const filelen = filename.length;    // 문자열의 길이
    const lastdot = filename.lastIndexOf('.');  //마지막 .의 인덱스
    return filename.substring(lastdot + 1, filelen).toLowerCase();
  }
  
  
  document.addEventListener("DOMContentLoaded", () => {
  
    const params = getUrlParams()
  
    const btn_file_dels = document.querySelectorAll(".btn_file_del")  // board_edit.php 파일 삭제 버튼
    btn_file_dels.forEach((box) => {
      box.addEventListener("click", () => {
  
        if(!confirm('해당 첨부파일을 삭제하시겠습니까?'))  {
          return false
        }
  
        const f = new FormData()
        f.append("th", box.dataset.th)      // 한 게시글 내의 첨부파일 번호. 최대 0~2
        f.append("bcode", params['bcode'])  // 게시판 코드
        f.append("idx", params['idx'])      // 게시물 번호
        f.append("mode", "each_file_del")   // 모드 : 개별파일 삭제
    
  
        const xhr = new XMLHttpRequest()  // 서버와 상호작용할 때 사용되는 객체로, 웹 페이지가 전체를 새로 고침하지 않고도 서버로부터 데이터를 요청하고 받을 수 있게 해줌
        xhr.open("post", "pg/board_process.php", true)  //어떤 방식을 사용할지, 어떤 자료가 필요한지, 비동기 처리 여부

        xhr.send(f)   //send 사용자 요청을 서버로 보냄. xhr.open에서 처리방식을 POST로 처리했기 때문에 매개변수가 존재(GET이면 null 혹은 비움)

        xhr.onload = () => {
          if(xhr.status == 200) {
  
            const data = JSON.parse(xhr.responseText)
            if(data.result == 'empty_idx') {
              alert('게시물 번호가 빠졌습니다.')
            }
            else if(data.result == 'empty_th') {
              alert('몇번째 첨부파일인지 알 수가 없습니다.')
            }
            else if(data.result == 'success') {
              self.location.reload()
            }
  
          }else if(xhr.status == 404) {
            alert('파일이 없습니다.')
          }
        }
  
      })
    })
  
    const id_attach = document.querySelector("#id_attach")  // 파일 첨부 입력 요소를 선택
    if(id_attach) {
      id_attach.addEventListener("change", () => {
        const f = new FormData()

        // FormData 객체에 게시판 코드, 모드, 게시물 번호를 추가합니다.
        f.append("bcode", params['bcode'])  // 게시판 코드
        f.append("mode", "file_attach")     // 모드 : 파일만 첨부
        f.append("idx", params['idx'])      // 게시물 번호

        //HTML에서 파일 입력 요소 (<input type="file">)는 사용자가 한 번에 여러 파일을 선택할 수 있도록 지원
        //선택된 파일들은 FileList 객체에 담기고, 이 FileList 객체는 files라는 속성을 통해 접근 가능
        if(id_attach.files[0].size > 40 * 1024 * 1024) {    //files[0]은 FileList에 담긴 파일들 중 첫번째 파일
          alert('파일 용량이 40메가보다 큰 파일이 첨부되었습니다. 확인 바랍니다.')
          id_attach.value = ''
          return false
        }
  
        ext = getExtensionOfFilename(id_attach.files[0].name);  //파일 이름에서 확장자 추출해서 ext에 저장
        if(ext == 'txt' || ext == 'exe' || ext == 'xls' || ext == 'php' || ext == 'js') {
          alert('첨부할 수 없는 포멧의 파일이 첨부되었습니다.(exe, txt, php, js ..)')
          id_attach.value = ''
          return false
        }
  
        f.append("files[]", id_attach.files[0])   // 파일 첨부

        // 새로운 XMLHttpRequest 객체를 생성하여 서버와 비동기 통신
        const xhr = new XMLHttpRequest()
        xhr.open("post", "./pg/board_process.php", true)    // POST 방식으로 "./pg/board_process.php" URL에 요청
        xhr.send(f)

        //// 서버 응답이 도착했을 때 실행되는 콜백 함수
        xhr.onload = () => {
          if(xhr.status == 200) {   // 서버 응답이 성공적이면
            const data = JSON.parse(xhr.responseText)   // JSON 형식의 응답을 JavaScript 객체로 변환
            if(data.result == 'success') {
              self.location.reload()
            } else if(data.result == 'empty_files') {
              alert('파일이 첨부되지 않았습니다.')
            }
          } else if(xhr.status == 404) {
            alert('통신 실패')
          }
        }
          
      })
      
    }

    //목록으로 이동
    const btn_board_list = document.querySelector("#btn_board_list")
    btn_board_list.addEventListener("click", () => {
      self.location.href='./board.php?bcode=' + params['bcode'];
    })
  
    // 수정확인 버튼 클릭시
    const btn_edit_submit = document.querySelector("#btn_edit_submit")
    btn_edit_submit.addEventListener("click", () => {
        const id_subject = document.querySelector("#id_subject");
        if(id_subject.value == '') {
            alert('게시물 제목을 입력해 주세요')
            id_subject.focus()
            return false  
        }
      
        const markupStr = $('#summernote').summernote('code')
        if(markupStr == '<p><br></p>') {
            alert('내용을 입력하세요.')
            return false
        }
  
        const params = getUrlParams()
    
        const f = new FormData()
        f.append("subject", id_subject.value) // 게시물 제목
        f.append("content", markupStr) // 게시물 내용
        f.append("bcode", params['bcode']) // 게시판 코드
        f.append("idx", params['idx']) // 게시물 번호
        f.append("mode", "edit") // 모드 : 글 수정

        const xhr = new XMLHttpRequest()    // 또 새로운 XMLHttpRequest 객체 생성, 서버와 비동기 통신
        xhr.open("post", "./pg/board_process.php", true)  // POST 방식으로 "./pg/board_process.php" URL에 요청
        xhr.send(f)
    
        xhr.onload = () => {
            if (xhr.status == 200) {
                try {
                    const data = JSON.parse(xhr.responseText);    // JSON 형식의 응답을 JavaScript 객체로 변환
                    if (data.result == 'success') {
                        alert('글 수정이 성공했습니다.');
                        self.location.href = './board.php?bcode=' + params['bcode'];
                    } else if (data.result == 'permission_denied') {
                        alert('수정 권한이 없는 게시물입니다.');
                        self.location.href = './board.php?bcode=' + params['bcode'];
                    }
                } catch (e) {
                    console.error("JSON 파싱 오류: ", e);
                    console.error("응답 텍스트: ", xhr.responseText);
                }
            } else if (xhr.status == 404) {
                alert('통신실패, 파일이 없습니다.');
            }
        }
  
  
    })
  
  }) // DOMContentLoaded