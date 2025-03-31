'use client';

import { useEffect, useState } from "react";

export default function CoffeeProgress() {
    const [progress, setProgress] = useState<number[]>([]);
    const [status, setStatus] = useState('Préparation en cours...');    

    useEffect(() => {
        console.log("useEffect");
        const eventSource = new EventSource("http://localhost:8081/.well-known/mercure?topic=coffee/progress");

        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            const newProgressValue = Math.floor(parseInt(data, 10) * 1.67);
            setProgress((prevProgress) => { 
                const updatedProgress = [...prevProgress, newProgressValue];

                if (updatedProgress[updatedProgress.length - 1] === 100) {
                    setStatus("Votre commande est prête !");
                }
                return updatedProgress;
            });
        };

        eventSource.onerror = () => {
            setStatus("Erreur de connexion au serveur.");
            eventSource.close();
        }

        return () => {
            if (progress[progress.length -1] === 100) {
                setStatus("Votre commande est prête !");
                eventSource.close();
            }
        }

    }, [progress]);

    return (
        <div>
            <h2>{status}</h2>
            <progress value={progress[progress.length -1]} max="100"></progress>
            <p>kn: {progress[progress.length -1]}</p>
        </div>
    );
}
