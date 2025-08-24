function calculateC(L, T, P, row) {
    // Ensure T and P are even numbers
    if (T % 2 !== 0 || P % 2 !== 0) {
        alert("T and P should be even numbers.");
        row.querySelector(".T").value = row.querySelector(".T").dataset.previousValue;
        row.querySelector(".P").value = row.querySelector(".P").dataset.previousValue;
        return;
    }

    // Calculate C
    var C = L + (T / 2) + (P / 2);

    // If C exceeds 4, reset all values
    if (C > 4) {
        alert("C cannot exceed 4");
        row.querySelector(".L").value = 0;
        row.querySelector(".T").value = 0;
        row.querySelector(".P").value = 0;
        row.querySelector(".C").value = 0;
        return;
    }

    // Update C value
    row.querySelector(".C").value = C;
}

function handleInputChange(event) {
    var row = event.target.closest("tr");
    var L = parseFloat(row.querySelector(".L").value) || 0;
    var T = parseFloat(row.querySelector(".T").value) || 0;
    var P = parseFloat(row.querySelector(".P").value) || 0;

    // Store previous values of T and P for validation
    row.querySelector(".T").dataset.previousValue = row.querySelector(".T").value;
    row.querySelector(".P").dataset.previousValue = row.querySelector(".P").value;

    // Call the calculateC function to update C
    calculateC(L, T, P, row);
}

function deleteCourse(courseId) {
    if (confirm("Are you sure you want to delete this course?")) {
        // Perform AJAX or form submission to delete the course
        window.location.href = "cdelete.php?id=" + courseId;
    }
}
