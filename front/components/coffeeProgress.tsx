'use client';

import { useEffect, useState } from "react";

export default function CoffeeProgress() {
    const [progress, setProgress] = useState(0);
    const [status, setStatus] = useState('Préparation en cours...');

    useEffect(() => {
        const eventSource = new EventSource("http://localhost:8081/.well-known/mercure?topic=coffee/progress");

        eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        console.log(data);
        setProgress(data.progress);
        };

        eventSource.onerror = () => {
            setStatus("Erreur de connexion au serveur.");
            eventSource.close();
        }

        return () => eventSource.close();
    },[]);

    return (
        <div>
            <h2>Préparation du café...</h2>
            <progress value={progress} max="100"></progress>
            <p>{progress}</p>
            <p>{status}</p>
        </div>
    );
}
