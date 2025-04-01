import { useEffect, useState } from 'react';

type Queue = {
    name: string;
    messages: number;
};

const Queues = () => {
    const [queues, setQueues] = useState<Queue[]>([]);

    useEffect(() => {
        const fetchQueues = async () => {
            const response = await fetch('http://localhost:8001/api/queues');
            if(response.ok) {
                const data = await response.json();
                setQueues(data);
            }

            const eventSource = new EventSource('http://localhost:8081/.well-known/mercure?topic=/queues');

            eventSource.onmessage = (event) => {
                const updatedQueues = JSON.parse(event.data);
                setQueues(updatedQueues);
            };

            return () => {
                eventSource.close();
            };
        };

        fetchQueues();
    }, []);

  return (
    <div>
      <h1>Liste des files d&apos;attente RabbitMQ (temps réel)</h1>
      <ul>
        {queues.map((queue, index) => (
          <li key={index}>
            <h3>{queue.name}</h3>
            <p>Messages: {queue.messages}</p>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default Queues;
