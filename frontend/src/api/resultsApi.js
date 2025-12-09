import api from "./httpClient";

export async function getResults() {
  const response = await api.get("/results");
  return response.data; 
}
