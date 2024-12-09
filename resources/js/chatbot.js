import Groq from "groq-sdk";

// Permitir el uso en navegador con un aviso explícito de riesgos
const groq = new Groq({
    apiKey: import.meta.env.VITE_GROQ_API_KEY,
    dangerouslyAllowBrowser: true, // Solo para pruebas
});

export async function getChatCompletion(userMessage) {
    try {
        const response = await groq.chat.completions.create({
            messages: [{ role: "user", content: userMessage }],
            model: "llama3-8b-8192",
        });
        return response.choices[0]?.message?.content || "No response from the model.";
    } catch (error) {
        console.error("Error fetching chat completion:", error);
        return "An error occurred while processing your request.";
    }
}