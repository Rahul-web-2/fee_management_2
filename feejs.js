document.addEventListener("DOMContentLoaded", function () {
    // Attach event listeners to dropdowns
    document.getElementById('studenttype').addEventListener('change', calculateTotalFee);
    document.getElementById('category').addEventListener('change', calculateTotalFee);
});

function calculateTotalFee() {
    const studenttype = document.getElementById('studenttype').value;
    const category = document.getElementById('category').value;
    const totalfeeField = document.getElementById('totalfee');

    console.log("Student Type Selected:", studenttype);
    console.log("Category Selected:", category);

    // Constants for base fees and discounts
    const BASE_FEES = {
        DayScholar: 16000,
        Hosteler: 16800
    };
    const DISCOUNTS = {
        TFW: 8000,
        General: 0
    };

    // Validate inputs
    if (!studenttype || !category) {
        totalfeeField.value = ""; // Clear the total fee field if inputs are invalid
        console.warn("Invalid input: Both student type and category must be selected.");
        return;
    }

    // Get base fee based on student type
    const baseFee = BASE_FEES[studenttype] || 0;

    // Get discount based on category
    const discount = DISCOUNTS[category] || 0;

    // Calculate total fee
    let totalFee = baseFee - discount;

    // Ensure total fee is never negative
    totalFee = Math.max(totalFee, 0);

    // Debugging output
    console.log("Base Fee:", baseFee);
    console.log("Discount:", discount);
    console.log("Calculated Total Fee:", totalFee);

    // Update the total fee field
    totalfeeField.value = totalFee.toFixed(2); // Set as formatted number
}