module Claspx
  class ClaspCmd < Cmd
    def initialize()
      result = WhichCmd.new.which("npx")
      super("#{result.return_value} clasp")
    end

    def pull(chdir)
      cmdline = "#{@name} pull"
      execute(cmdline, chdir)
    end

    def push(chdir)
      cmdline = "#{@name} push"
      # p cmdline
      execute(cmdline, chdir)
    end
  end
end