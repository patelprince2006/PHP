
import { GoogleGenAI } from "@google/genai";

const ai = new GoogleGenAI({ apiKey: process.env.API_KEY || "" });

export const getProductAdvice = async (query: string) => {
  try {
    const response = await ai.models.generateContent({
      model: "gemini-3-flash-preview",
      contents: `User is asking about Nutra_leaf products: "${query}". 
      Nutra_leaf sells two products: 
      1. Ashwagandha Gold (Stress, Sleep, Focus) - ₹1299
      2. Moringa Power (Nutrition, Energy, Immunity) - ₹849
      Provide a helpful, short, professional health-focused recommendation in 2-3 sentences.`,
    });
    return response.text;
  } catch (error) {
    console.error("Gemini Error:", error);
    return "I'm currently unable to provide advice. Please consult our product descriptions.";
  }
};
