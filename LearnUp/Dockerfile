# Use Node.js as base
FROM node:20

# Set working directory
WORKDIR /app

# Copy package.json and install dependencies
COPY package.json package-lock.json ./

# Install dependencies
RUN npm install

# Copy other frontend files
COPY . .

# Build the frontend
RUN npm run build

# Expose port 3000
EXPOSE 3000

# Start the React app
CMD ["npm","run", "dev"]
