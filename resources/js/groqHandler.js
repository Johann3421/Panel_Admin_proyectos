import Groq from "groq-sdk";

// Configura el SDK de Groq con tu clave API directamente
const groq = new Groq({
    apiKey: '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', // Reemplaza con tu clave API real
});

export async function getChatCompletion(userMessage) {
    try {
        const response = await groq.chat.completions.create({
            messages: [{ role: "user", content: userMessage }],
            model: "llama3-8b-8192",
        });
        console.log(response.choices[0]?.message?.content || "No response from the model.");
    } catch (error) {
        console.error("Error fetching chat completion:", error);
    }
}

// Captura el argumento desde la línea de comandos
const userMessage = process.argv[2];
if (userMessage) {
    getChatCompletion(userMessage);
} else {
    console.error("No message provided. Usage: node groqHandler.js <message>");
}
