<div class="pass_rec">
  <span style="display: inline-block"><?php echo gdrcd_filter('out', $MESSAGE['homepage']['forms']['forgot']); ?></span>
  
  <form id="passRecoveryForm">
    <input type="text" id="passrecovery" name="email" placeholder="email"/>
    <div id="feedback_box" style="margin-top:10px;color:red;"></div>
    <input type="submit" value="<?php echo $MESSAGE['homepage']['forms']['new_pass']; ?>"/>
  </form>
</div>

<script>
  document.getElementById('passRecoveryForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('passrecovery').value;

    fetch('pages/homepage/reset_password_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.text())
    .then(feedback => {
      document.getElementById('feedback_box').innerHTML = feedback;
     
      if (feedback.includes('modificata') || feedback.includes('inviata')) {
        closeModalWindow('scheda_reset');
      }
    });
  });
</script>