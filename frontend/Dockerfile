# Use a lightweight Node image
FROM node:20-alpine

# Create and set the working directory
WORKDIR /app

# Copy dependency files first to leverage Docker layer caching
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application code
COPY . .

# Expose the Vite port (default 5173)
EXPOSE 5173

# Start the development server with hot reload
CMD ["npm", "run", "dev"]
