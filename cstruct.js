document.querySelector('.load-btn').addEventListener('click', () => {
    const year = document.querySelector('#year-select').value;
    const semester = document.querySelector('#semester-select').value;

    if (!year || !semester) {
        alert('Please select both Year and Semester!');
    } else {
        alert(`Loading course structure for Year: ${year}, Semester: ${semester}`);
    }
});
