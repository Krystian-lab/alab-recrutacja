import api from "./httpClient";

export async function login(login, password) {
  const response = await api.post("/login", {
    login,
    password,
  });
  return response.data; 
}
