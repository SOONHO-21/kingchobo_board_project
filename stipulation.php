<?php
  
$js_array = ['js/member.js'];

$g_title = '약관';
$menu_code = 'member';

include 'inc_header.php';

?>
  <main>
    <h1 class="text-center mt-5">회원 약관 및 개인정보 취급방침 동의</h1>
    <h4>회원약관</h4>
    <textarea name="" id="" cols="" rows="" class="form-control">
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Non consequatur iusto ad, ullam debitis sint dolorem dicta velit fugiat asperiores libero illo corrupti inventore impedit sed unde est laboriosam nam.
    </textarea>

    <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" value="1" id="chk_member1">
        <label class="form-check-label" for="chk_member1">
          위 약관에 동의 하시겠습니까?
        </label>
      </div>

    <h4>개인정보 취급 방침</h4>
    <textarea name="" id="" cols="" rows="" class="form-control">
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Non consequatur iusto ad, ullam debitis sint dolorem dicta velit fugiat asperiores libero illo corrupti inventore impedit sed unde est laboriosam nam.
    </textarea>

    <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" value="2" id="chk_member2">
        <label class="form-check-label" for="chk_member2">
          위 개인정보 취급 방침에 동의 하시겠습니까?
        </label>
    </div>

    <div class="mt-4 d-flex justify-content-center gap-2">
        <button class="btn btn-primary w-50" id="btn_member">회원가입</button>
        <button class="btn btn-secondary w-50">가입취소</button>
    </div>

    <form method="post" name="stipulation_form" action="member_input.php">
      <input type="hidden" name="chk" value="0">
    </form>
  </main>
<?php include 'inc_footer.php'; ?>