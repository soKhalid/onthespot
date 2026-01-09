FROM node:18-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy application files
COPY . .

# Expose port
EXPOSE 3000

# Start development server (for production, use build and serve)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
