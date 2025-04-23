import { useEffect, useState } from 'react';

type Queue = {
    node: string;
    messages: number;
};

const Queues = () => {
    const [queue, setQueue] = useState<Queue>({'node': '', 'messages': 0});

    const displayEvents = () => {
      const eventSource = new EventSource("http://localhost:8081/.well-known/mercure?topic=https://example.com/books/1");

      eventSource.onmessage = event => {
        const data = JSON.parse(event.data);
        // console.log(data.message[0]);
        if (data && data.message) setQueue(data.message[0]);
      };

      // Clean up the EventSource on component unmount
      return () => {
        eventSource.close();
      };
    };

    useEffect(() => {
          displayEvents();
    }, []);

  return (
    <div>
      <h1 className="font-bold">Liste des files d&apos;attente RabbitMQ (temps réel)</h1>
        <h3>Instance: {queue && queue.node}</h3>
        <p>Messages: {queue && queue.messages}</p>
    </div>
  );
};

export default Queues;
