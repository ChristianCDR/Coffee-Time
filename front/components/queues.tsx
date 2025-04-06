import { useEffect, useState } from 'react';

type Queue = {
    name: string;
    messages: number;
};

const Queues = () => {
    const [queues, setQueues] = useState<Queue[]>([]);
    // const [jwt, setJwt] = useState<string | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    

    useEffect(() => {

      const fetchQueues = async () => {

        setLoading(true);

        try {
          const response = await fetch('http://localhost:8001/api/queues')

          if (!response.ok) {
            throw new Error('Failed to fetch initial queues')
          }

          const data = await response.json()
          
          console.log(data)
          // setJwt(data.jwt)

          // Store the JWT in a cookie
          document.cookie = `Authorization=Bearer ${data.jwt}; path=/;`;

          const url = new URL('http://localhost:8081/.well-known/mercure');
          url.searchParams.append('topic', 'http://localhost/queues');

          const eventSource = new EventSource(url.toString(), {withCredentials: true});

          eventSource.onmessage = (event) => {
            const updatedQueues = JSON.parse(event.data)
            console.log(updatedQueues)
            setQueues(updatedQueues)
          }

          eventSource.onerror = (event) => {
            console.error('EventSource failed:', event);
            setError('EventSource failed');
          };

          return () => {
            eventSource.close();
          };
          
        } catch (error: string | unknown) {
          console.error('Error fetching queues:', error)
          if (error) setError(error.toString())
            
        } finally {
          setLoading(false)
        }
      }

      fetchQueues();      

      

        
    }, []);

    if (loading) {
      return <div>Loading...</div>
    }
  
    if (error) {
      return <div>Error: {error}</div>
    }

  return (
    <div>
      <h1 className="font-bold">Liste des files d&apos;attente RabbitMQ (temps réel)</h1>
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
