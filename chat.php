
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dental Clinic</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet" />

  <style>
    :root {
      --primary-color: #5b94f6ff;
      --secondary-color: #f0f8ff;
      --bot-bubble-color: #e9ecef;
      --user-bubble-color: #48A6A7;
      --text-color: #333;
      --body-bg: #f4f7f6;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Lato', sans-serif;
      background-color: var(--body-bg);
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Floating Chat Button */
    #chat-toggle {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      font-size: 30px;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
      display: flex;
      justify-content: center;
      align-items: center;
      transition: background-color 0.3s, transform 0.3s;
      z-index: 999;
    }

    #chat-toggle:hover {
      background-color: #0056b3;
      transform: scale(1.1);
    }

    /* Chat Widget Window */
    #chat-widget {
      position: fixed;
      bottom: 90px;
      right: 20px;
      width: 380px;
      height: 560px;
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
      display: none;
      flex-direction: column;
      overflow: hidden;
      border: 1px solid #ddd;
      z-index: 5000;
      animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    #chat-header {
      background-color: var(--primary-color);
      color: white;
      padding: 15px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    #chat-header h3 {
      margin: 0;
      font-size: 1.1em;
    }

    #close-chat {
      background: transparent;
      border: none;
      color: white;
      font-size: 1.3em;
      cursor: pointer;
    }

    #chat-box {
      flex-grow: 1;
      padding: 15px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 10px;
      scroll-behavior: smooth;
    }

    .chat-message {
      padding: 10px 14px;
      border-radius: 20px;
      max-width: 85%;
      line-height: 1.4;
      font-size: 0.95em;
      white-space: pre-wrap;
      word-wrap: break-word;
      border: 1px solid black;
    }

    .user-message {
      background-color: var(--user-bubble-color);
      color: white;
      align-self: flex-end;
      border-bottom-right-radius: 5px;
    }

    .bot-message {
      background-color: var(--bot-bubble-color);
      color: var(--text-color);
      align-self: flex-start;
      border-bottom-left-radius: 5px;
    }

    #chat-form-container {
      background-color: #ffffff;
      padding: 10px;
      border-top: 1px solid #eee;
    }

    #chat-form {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    #user-input {
      flex-grow: 1;
      border: 1px solid black;
      border-radius: 20px;
      padding: 10px 15px;
      font-size: 0.95em;
      outline: none;
      transition: border-color 0.3s;
    }

    #user-input:focus {
      border-color: var(--primary-color);
    }

    #send-btn {
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      cursor: pointer;
      font-size: 1.2em;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    #send-btn:hover {
      background-color: #0056b3;
    }

    /* 📱 Responsive Adjustments */
    @media (max-width: 768px) {
      #chat-widget {
        width: 90%;
        height: 75%;
        bottom: 80px;
        right: 5%;
        left: 5%;
        border-radius: 12px;
      }

      #chat-header h3 {
        font-size: 1em;
      }

      #chat-toggle {
        width: 55px;
        height: 55px;
        font-size: 26px;
        bottom: 15px;
        right: 15px;
      }

      .chat-message {
        font-size: 0.9em;
      }
    }

    @media (max-width: 480px) {
      #chat-widget {
        width: 95%;
        height: 80%;
        bottom: 70px;
        right: 2.5%;
        left: 2.5%;
      }

      #chat-header h3 {
        font-size: 0.9em;
      }

      #chat-toggle {
        width: 50px;
        height: 50px;
        font-size: 24px;
      }

      #user-input {
        font-size: 0.85em;
        padding: 8px 12px;
      }

      #send-btn {
        width: 36px;
        height: 36px;
        font-size: 1em;
      }
    }
  </style>
</head>

<body>
  <!-- Floating chat toggle button -->
  <button id="chat-toggle" title="Chat with us"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF"><path d="M240-520h60v-80h-60v80Zm100 80h60v-240h-60v240Zm110 80h60v-400h-60v400Zm110-80h60v-240h-60v240Zm100-80h60v-80h-60v80ZM80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z"/></svg></button>

  <!-- Chat widget -->
  <div id="chat-widget">
    <div id="chat-header">
      <h3>Dental Clinic Assistant</h3>
      <button id="close-chat" title="Close">&times;</button>
    </div>

    <div id="chat-box">
      <div class="chat-message bot-message">
        Hello! 👋 Welcome to the Dental Clinic. How can I help you today?  
        You can ask about our services, schedule, or booking an appointment.
      </div>
    </div>

    <div id="chat-form-container">
      <form id="chat-form">
        <input type="text" id="user-input" placeholder="Type your message..." autocomplete="off" />
        <button id="send-btn" type="submit">➤</button>
      </form>
    </div>
  </div>

  <script>
    const chatToggle = document.getElementById('chat-toggle');
    const chatWidget = document.getElementById('chat-widget');
    const closeChat = document.getElementById('close-chat');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');
    const chatBox = document.getElementById('chat-box');
    const sendBtn = document.getElementById('send-btn');

    // Show chat
    chatToggle.addEventListener('click', () => {
      chatWidget.style.display = 'flex';
      chatToggle.style.display = 'none';
    });

    // Close chat
    closeChat.addEventListener('click', () => {
      chatWidget.style.display = 'none';
      chatToggle.style.display = 'flex';
    });

    // Send message
    chatForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const messageText = userInput.value.trim();
      if (messageText === '') return;

      addMessage(messageText, 'user');
      userInput.value = '';
      showTypingIndicator();
      sendMessageToBot(messageText);
    });

    function addMessage(text, sender) {
      const messageElement = document.createElement('div');
      messageElement.classList.add('chat-message', sender + '-message');
      messageElement.innerHTML = text.replace(/\n/g, '<br>');
      chatBox.appendChild(messageElement);
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    function showTypingIndicator() {
      if (document.querySelector('.typing')) return;
      const messageElement = document.createElement('div');
      messageElement.classList.add('chat-message', 'bot-message', 'typing');
      messageElement.textContent = 'Typing...';
      chatBox.appendChild(messageElement);
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    function removeTypingIndicator() {
      const typingElement = document.querySelector('.typing');
      if (typingElement) typingElement.remove();
    }

    async function sendMessageToBot(message) {
      sendBtn.disabled = true;

      try {
        const response = await fetch('chatbot.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ message })
        });

        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();
        removeTypingIndicator();
        addMessage(data.reply, 'bot');
      } catch (error) {
        console.error('Error:', error);
        removeTypingIndicator();
        addMessage('⚠️ Oops! Something went wrong. Please try again later.', 'bot');
      } finally {
        sendBtn.disabled = false;
        userInput.focus();
      }
    }
  </script>
</body>
</html>
