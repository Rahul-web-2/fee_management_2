document.addEventListener("DOMContentLoaded", function () {
    const feeForm = document.getElementById("feeForm");
    const rzpButton = document.getElementById("rzp-button1");

    rzpButton.addEventListener("click", function (e) {
        e.preventDefault();

        // Collect form data
        const formData = new FormData(feeForm);
        const data = Object.fromEntries(formData.entries());

        // Validate form data
        if (!data.name || !data.rollnumber || !data.branch || !data.semesteryear || !data.studenttype || !data.category || !data.totalfee) {
            alert("Please fill out all fields.");
            return;
        }

        if (isNaN(data.totalfee) || parseFloat(data.totalfee) <= 0) {
            alert("Total fee must be a valid positive number.");
            return;
        }

        // Razorpay options
        const options = {
            key: "Razorpay Key ID", // Replace with your Razorpay Key ID
            amount: parseFloat(data.totalfee) * 100, // Convert to paise
            currency: "INR",
            name: "Institute Name",
            description: "Fee Payment",
            handler: function (response) {
                // Send payment details and form data to the server
                fetch("fees.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        ...data,
                        payment_id: response.razorpay_payment_id,
                    }),
                })
                    .then((res) => res.json())
                    .then((result) => {
                        if (result.success) {
                            alert("Payment successful! Downloading receipt...");
                            // Redirect to fee_receipt.php with rollnumber and payment_id
                            window.location.href = `fee_receipt.php?rollnumber=${encodeURIComponent(data.rollnumber)}&payment_id=${encodeURIComponent(response.razorpay_payment_id)}`;
                        } else {
                            alert(result.message || "Payment failed. Please try again.");
                        }
                    })
                    .catch((err) => {
                        console.error("Error:", err);
                        alert("An error occurred while processing your payment. Please try again.");
                    });
            },
            prefill: {
                name: data.name,
                email: "student@example.com", // Optional
                contact: "919876543210", // Optional
            },
            theme: {
                color: "#3399cc",
            },
        };

        const rzp = new Razorpay(options);
        rzp.open();
    });
});