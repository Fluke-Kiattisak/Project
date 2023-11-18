document.addEventListener('DOMContentLoaded', () => {
    // Fetch user profile data and render it in the #user-profile section
    fetch('/user/123')
        .then(response => response.json())
        .then(user => {
            const userProfileSection = document.getElementById('user-profile');
            userProfileSection.innerHTML = `
                <h2>${user.Username}'s Profile</h2>
                <p>Email: ${user.Email}</p>
                <p>Registration Date: ${user.RegistrationDate}</p>
            `;
        })
        .catch(error => console.error('Error fetching user profile:', error));

    // Fetch user predictions data and render it in the #user-predictions section
    fetch('/user/123/predictions') 
        .then(response => response.json())
        .then(predictions => {
            const userPredictionsSection = document.getElementById('user-predictions');
            userPredictionsSection.innerHTML = '<h2>Predictions</h2>';

            predictions.forEach(prediction => {
                userPredictionsSection.innerHTML += `
                    <div>
                        <p>Date of Prediction: ${prediction.DateOfPrediction}</p>
                        <p>Predicted Price: ${prediction.PredictedPrice}</p>
                        <p>Actual Price: ${prediction.ActualPrice}</p>
                        <p>Trade Signal: ${prediction.TradeSignal}</p>
                        <p>RSI Signal: ${prediction.RSISignal}</p>
                    </div>
                `;
            });
        })
        .catch(error => console.error('Error fetching user predictions:', error));
});
