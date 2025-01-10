require "gemini-ai"

module Claspx
  class Geminiaix
    def initialize(model=nil)
      # With an API key
      model ||= "models/gemini-2.0-flash-thinking-exp-1219"
      @model = model
      @client = Gemini.new(
        credentials: {
          service: "generative-language-api",
          api_key: ENV["GOOGLE_API_KEY"]
        },
        options: { model: @model, server_sent_events: true }
      )
    end

    def generate_content(prompt=nil)
=begin
    # With a Service Account Credentials File
    client = Gemini.new(
      credentials: {
        service: "vertex-ai-api",
        file_path: "google-credentials.json",
        region: "us-east4"
      },
      options: { model: @model, server_sent_events: true }
    )
=end

=begin
    # With the Service Account Credentials File contents
    client = Gemini.new(
      credentials: {
        service: "vertex-ai-api",
        file_contents: File.read("google-credentials.json"),
        # file_contents: ENV['GOOGLE_CREDENTIALS_FILE_CONTENTS'],
        region: "us-east4"
      },
      options: { model: @model, server_sent_events: true }
    )
=end

=begin
      # With Application Default Credentials
      @client = Gemini.new(
        credentials: {
          service: "vertex-ai-api",
          region: "us-east4"
        },
        options: { model: "gemini-pro", server_sent_events: true }
      )
=end
      result = @client.stream_generate_content({
                                                contents: { role: "user", parts: { text: "hi!" } }
                                              })
    end
  end
end
