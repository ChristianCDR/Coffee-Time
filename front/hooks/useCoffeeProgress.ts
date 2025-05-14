import { useEffect, useState } from "react";
import { UseCoffeeProgressReturn } from "@/types/index";

export function useCoffeeProgress<T>(): UseCoffeeProgressReturn<T> {
    const [data, setData] = useState<T | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const eventSource = new EventSource("http://localhost:8081/.well-known/mercure?topic=https://example.com/books/1");

        eventSource.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                setData(data);
            }
            catch (err) {
                setError("Erreur lors du traitement des données reçues.");
            } 
        };

        eventSource.onerror = (err) => {
            console.error("Erreur EventSource :", err);
            setError("Impossible de se connecter au serveur. Veuillez réessayer plus tard.");
        }

        return () => {
            eventSource.close();
        };

    }, []);

    return { data, error };
}