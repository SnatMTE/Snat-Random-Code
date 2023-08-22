//Written by Snat - https://snat.co.uk

using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Threading.Tasks;

class Program
{
    static async Task Main(string[] args)
    {
        string accessToken = "YOUR_USER_ACCESS_TOKEN";
        string pageId = "PAGE_ID"; 

        while (true)
        {
            List<string> previousPostIds = new List<string>(); // Store previous post IDs

            using (HttpClient client = new HttpClient())
            {
                string feedUrl = $"https://graph.facebook.com/v13.0/{pageId}/feed?access_token={accessToken}";

                HttpResponseMessage response = await client.GetAsync(feedUrl);

                if (response.IsSuccessStatusCode)
                {
                    string responseContent = await response.Content.ReadAsStringAsync();
                    dynamic responseData = Newtonsoft.Json.JsonConvert.DeserializeObject(responseContent);

                    foreach (var post in responseData.data)
                    {
                        string postId = post.id;
                        if (!previousPostIds.Contains(postId))
                        {
                            // A new post is found
                            previousPostIds.Add(postId);

                            // Call a function to post a comment
                            await PostComment(accessToken, postId, "Your comment here");
                        }
                    }
                }
                else
                {
                    Console.WriteLine($"Error fetching feed: {response.StatusCode}");
                }
            }

            // Polling interval (e.g., every 5 minutes)
            await Task.Delay(TimeSpan.FromMinutes(5));
        }
    }

    static async Task PostComment(string accessToken, string postId, string commentText)
    {
        using (HttpClient client = new HttpClient())
        {
            string commentUrl = $"https://graph.facebook.com/v13.0/{postId}/comments";
            
            var content = new FormUrlEncodedContent(new[]
            {
                new KeyValuePair<string, string>("message", commentText),
                new KeyValuePair<string, string>("access_token", accessToken)
            });

            HttpResponseMessage response = await client.PostAsync(commentUrl, content);

            if (response.IsSuccessStatusCode)
            {
                string responseContent = await response.Content.ReadAsStringAsync();
                dynamic responseData = Newtonsoft.Json.JsonConvert.DeserializeObject(responseContent);

                if (responseData.id != null)
                {
                    Console.WriteLine("Comment posted successfully!");
                }
                else
                {
                    Console.WriteLine($"Error posting comment: {responseContent}");
                }
            }
            else
            {
                Console.WriteLine($"Error sending HTTP request: {response.StatusCode}");
            }
        }
    }
}
