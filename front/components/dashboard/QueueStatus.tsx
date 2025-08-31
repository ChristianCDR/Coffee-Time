'use client';

import { useQueueStatus } from "@/hooks/useQueueStatus";

export default function QueueStatus () {

  const { queue, error } = useQueueStatus();

  if (error) return <p className="text-red-500">{error}</p>;
  
  return (
    <div>
      <h1 className="font-bold">Liste des fils d&apos;attente RabbitMQ (temps réel)</h1>
        <h3>Instance: { queue.node }</h3>
        <p>Messages: { queue.messages }</p>
    </div>
  );
};
