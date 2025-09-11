document.addEventListener("DOMContentLoaded", function() {
    const steps = ['Step 1', 'Step 2', 'Step 3', 'Step 4'];
    const container = document.getElementById('timeline-container');

    const timeline = document.createElement('div');
    timeline.className = 'timeline';

    // สร้าง timeline steps
    steps.forEach((step, index) => {
        const stepDiv = document.createElement('div');
        stepDiv.className = 'timeline-step';
        stepDiv.id = 'step' + (index + 1);

        const circle = document.createElement('div');
        circle.className = 'circle';

        const p = document.createElement('p');
        p.textContent = step;

        stepDiv.appendChild(circle);
        stepDiv.appendChild(p);
        timeline.appendChild(stepDiv);
    });

    container.appendChild(timeline);

    // ฟังก์ชันอัพเดตขั้นตอน
    function updateSteps() {
        const allSteps = document.querySelectorAll('.timeline-step');
        allSteps.forEach(step => step.classList.remove('active', 'completed'));

        const activeStep = document.querySelector('.timeline-step .circle.active')?.parentElement;
        if (activeStep) {
            activeStep.classList.add('active');
            let prev = activeStep.previousElementSibling;
            while (prev) {
                prev.classList.add('completed');
                prev = prev.previousElementSibling;
            }
        }
    }

    // โหลดค่า active จาก localStorage
    const activeStepId = localStorage.getItem('activeStep');
    if (activeStepId) {
        const activeCircle = document.querySelector(`#${activeStepId} .circle`);
        if (activeCircle) activeCircle.classList.add('active');
        localStorage.removeItem('activeStep');
    }

    updateSteps();

    // Event click
    const allSteps = document.querySelectorAll('.timeline-step');
    allSteps.forEach(step => {
        step.addEventListener('click', function() {
            allSteps.forEach(s => s.querySelector('.circle').classList.remove('active'));
            this.querySelector('.circle').classList.add('active');
            updateSteps();

            localStorage.setItem('activeStep', this.id);
        });
    });
});
