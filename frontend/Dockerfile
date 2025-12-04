FROM node:20-alpine

WORKDIR /app

# Zależności
COPY package*.json ./
RUN npm install

# Reszta kodu
COPY . .

EXPOSE 5173

# Dev server Vite
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "5173"]
