'use client';

import { useEffect, useState } from "react";

type CoffeeProgressData = {
    progress: number;
    orderId: string;
};

export default function CoffeeProgress() { 
    const [data, setData] = useState<CoffeeProgressData>();

    useEffect(() => {
        const eventSource = new EventSource("http://localhost:8081/.well-known/mercure?topic=https://example.com/books/1");

        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log(data);
            setData(data);
        };

        return () => {
            eventSource.close();
        };

    }, []);

    return (
        <div>
            <h1 className="font-bold">État du Processus</h1>
            {data ? (
                <div>
                    <p>Commande: {data.orderId}</p>
                    <p>Progression: {data.progress}%</p>
                </div>
            ) : (
                <p>En attente de mise à jour...</p>
            )}
        </div>
    );
}
