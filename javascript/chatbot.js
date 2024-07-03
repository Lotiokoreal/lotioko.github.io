document.getElementById('chatWithAIButton').addEventListener('click', function() {
    document.getElementById('chatForm').style.display = 'flex';
});

document.getElementById('closeChatForm').addEventListener('click', function() {
    document.getElementById('chatForm').style.display = 'none';
});

document.getElementById('sendButton').addEventListener('click', async function() {
    const userInput = document.getElementById('userInput').value;
    if (!userInput) return;

    const chatOutput = document.getElementById('chatOutput');
    chatOutput.innerHTML += `<div><strong>Vous:</strong> ${userInput}</div>`;

    try {
        const response = await fetch('https://api.aimlapi.com/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer 249e20d5434d4e0a80f2217e047dc505' // Remplacez YOUR_API_KEY par votre clé API réelle
            },
            body: JSON.stringify({
                model: 'zero-one-ai/Yi-34B-Chat',
                prompt: userInput,
                max_tokens: 50,
                stop_sequences: ["\n\n"]
            })
        });

        if (!response.ok) {
            throw new Error('Trop dure pour moi (A cause de API)');
        }

        const data = await response.json();
        const aiResponse = data.completion.trim();
        chatOutput.innerHTML += `<div><strong>AI:</strong> ${aiResponse}</div>`;
    } catch (error) {
        console.error('Error:', error);
        chatOutput.innerHTML += `<div><strong>Erreur:</strong> ${error.message}</div>`;
    }

    document.getElementById('userInput').value = '';
    chatOutput.scrollTop = chatOutput.scrollHeight;
});
