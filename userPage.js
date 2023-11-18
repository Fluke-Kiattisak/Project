const express = require('express');
const app = express();
const http = require('http');
const fs =require('fs');
const path = require('path');
const mongoose = require('mongoose');
const { Prediction } = require('./database.js'); 


mongoose.connect('mongodb://mongo:27017/Final-project', {
  useNewUrlParser: true,
  useUnifiedTopology: true,
});

const server = http.createServer((res, req) => {
  if(req.url === '/'){
    const filePath = path.join(__dirname, 'public', 'index.html');
     fs.readFile(filePath, (err, data) => {
      if(err){
        res.writeHead(500, {'Content-type': 'text/plain'});
        res.end('Internal Server Error');
      }else{
        res.writeHead(200, {'Content-type' : 'text/head'});
        res.end(data);
      }
     });
  }
});
app.get('/user/:id', async (req, res) => {
    try {
      const userId = req.params.id;
      const user = await User.findOne({ UserID: userId });
  
      if (!user) {
        return res.status(404).json({ error: 'User not found' });
      }
  
      res.render('user-profile', { user });
    } catch (error) {
      res.status(500).json({ error: 'Internal server error' });
    }
  });
app.get('/user/:id/predictions', async (req, res) => {
  try {
    const userId = req.params.id;
    const predictions = await Prediction.find({ UserID: userId });

    if (predictions.length === 0) {
      return res.status(404).json({ error: 'No predictions found for this user' });
    }

    res.render('user-predictions', { predictions });
  } catch (error) {
    res.status(500).json({ error: 'Internal server error' });
  }
});
app.get('/prediction-schema', (req, res) => {
  const predictionSchema = Prediction.schema; // Get the schema of the Prediction model
  res.json(predictionSchema);
});

// Start the server
port = 3000;
app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});  