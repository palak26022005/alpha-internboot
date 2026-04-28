let examTimer;
let timeLeft = 30 * 60; // 30 minutes in seconds

// Make functions globally available
window.showProfile = async function() {
  try {
    const res = await fetch('get_profile.php');
    const student = await res.json();
    document.getElementById('profileSection').innerHTML =
      `<h2>Welcome ${student.first_name} ${student.last_name}!</h2>
       <p>Email: ${student.email}</p>`;
  } catch (error) {
    console.error('Error loading profile:', error);
    document.getElementById('profileSection').innerHTML = 
      '<p style="color: red;">Error loading profile. Please try again.</p>';
  }
};

window.showInstructions = function() {
  let html = `
    <h2>Exam Instructions</h2>
    <ul>
      <li>You must complete the test within 30 minutes.</li>
      <li>Do not switch tabs or minimize the window during the test.</li>
      <li>You need at least 20 correct answers out of 25 to download your LOI.</li>
      <li>If you score less than 20, you must retake the exam.</li>
    </ul>
    <label><input type="checkbox" id="agree"> I have read all the terms and conditions</label><br><br>
    <button onclick="startExam()" class="btn">Start Exam</button>
  `;
  document.getElementById('instructionSection').innerHTML = html;
  
  // Clear other sections when showing instructions
  document.getElementById('examSection').innerHTML = '';
  document.getElementById('resultSection').innerHTML = '';
};

window.startExam = async function() {
  if (!document.getElementById('agree').checked) {
    alert("Please agree to the terms and conditions before starting.");
    return;
  }

  try {
    const res = await fetch('get_exam_questions.php');
    const questions = await res.json();
    let html = '<h2>Exam (25 Questions)</h2>';
    html += '<div id="timer" style="font-weight:bold; color:red;"></div>';
    html += '<form id="examForm">';
    questions.forEach((q, i) => {
      html += `<p>${i+1}. ${q.question}</p>
               <label><input type="radio" name="q${q.id}" value="A"> ${q.option_a}</label><br>
               <label><input type="radio" name="q${q.id}" value="B"> ${q.option_b}</label><br>
               <label><input type="radio" name="q${q.id}" value="C"> ${q.option_c}</label><br>
               <label><input type="radio" name="q${q.id}" value="D"> ${q.option_d}</label><br>`;
    });
    html += '<button type="button" onclick="submitExam()">Submit Exam</button></form>';
    document.getElementById('examSection').innerHTML = html;

    // Clear instruction section and start exam
    document.getElementById('instructionSection').innerHTML = '';
    document.getElementById('resultSection').innerHTML = '';

    // Start timer
    timeLeft = 30 * 60;
    clearInterval(examTimer);
    examTimer = setInterval(updateTimer, 1000);
  } catch (error) {
    console.error('Error starting exam:', error);
    alert('Error loading exam questions. Please try again.');
  }
};

function updateTimer() {
  let minutes = Math.floor(timeLeft / 60);
  let seconds = timeLeft % 60;
  const timerElement = document.getElementById('timer');
  if (timerElement) {
    timerElement.innerText = 
      `Time Left: ${minutes}:${seconds < 10 ? '0'+seconds : seconds}`;
  }
  timeLeft--;
  if (timeLeft < 0) {
    clearInterval(examTimer);
    submitExam(); // auto submit
  }
}

window.submitExam = async function() {
  clearInterval(examTimer);
  const form = document.getElementById('examForm');
  if (!form) {
    console.error('Exam form not found');
    return;
  }

  const data = new FormData(form);
  let answers = {};
  for (let [key, value] of data.entries()) {
    answers[key.substring(1)] = value; // remove 'q' prefix
  }

  try {
    const res = await fetch('submit_exam.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({answers})
    });
    const result = await res.json();

    // Clear exam section
    document.getElementById('examSection').innerHTML = '';

    if (result.eligible) {
      alert("🎉 You cleared the test! You can now download your LOI.");
      document.getElementById('resultSection').innerHTML =
        `<h3>Score: ${result.score}/25 ✅</h3>
         <a href="download_loi.php" class="btn">Download LOI</a>`;
    } else {
      alert("❌ You did not clear the test. Please retake the exam.");
      document.getElementById('resultSection').innerHTML =
        `<h3>Score: ${result.score}/25 ❌</h3>
         <p>You need at least 20 correct answers. Please retake the exam.</p>
         <button onclick="showInstructions()" class="btn">Retake Exam</button>`;
    }
  } catch (error) {
    console.error('Error submitting exam:', error);
    alert('Error submitting exam. Please try again.');
  }
};
