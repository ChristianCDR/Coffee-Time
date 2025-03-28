# Etape de build
FROM node:23-alpine AS build

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build

# Etape de runtime

FROM node:18-alpine AS runtime

WORKDIR /app

COPY package*.json ./

RUN npm ci 
# --only=production

# Copie les fichiers de build
COPY --from=build /app/.next ./.next
COPY --from=build /app/public ./public

EXPOSE 3000

CMD ["npm", "run", "dev"]
# CMD ["npm", "run", "dev"]