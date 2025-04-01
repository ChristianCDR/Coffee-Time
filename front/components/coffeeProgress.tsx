'use client';

import { useEffect, useState } from "react";

type CoffeeProgressData = {
    status: string;
    progress: number;
};

export default function CoffeeProgress() {
    // const [progress, setProgress] = useState();
    // const [status, setStatus] = useState('Préparation en cours...');    
    const [data, setData] = useState<CoffeeProgressData>();

    useEffect(() => {
        const fetchCoffeeProgress = async () => {
            const response = await fetch('http://localhost:8001/api/queues');
            if(response.ok) {
                const data = await response.json();
                setData(data);
            }

            const eventSource = new EventSource("http://localhost:8081/.well-known/mercure?topic=" + encodeURIComponent('http://localhost/process/coffee'));

            eventSource.onmessage = (event) => {
                const data = JSON.parse(event.data);
                setData(data);
            };

            return () => {
                eventSource.close();
            };
        }
        
        fetchCoffeeProgress();

    }, []);

    return (
        <div>
            <h1>État du Processus</h1>
            {data ? (
                <div>
                    <p>Statut: {data.status}</p>
                    <p>Progression: {data.progress}%</p>
                </div>
            ) : (
                <p>En attente de mise à jour...</p>
            )}
        </div>
    );
}
